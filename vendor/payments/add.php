<?php
// vendor/payments/add.php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

requireRole('vendor');

$page_title = "Record Payment";
include '../../includes/header.php';

$user_id = $_SESSION['user_id'];
$db = new Database();
$conn = $db->getConnection();

// Fetch Customers
$stmt = $conn->prepare("SELECT customer_id, name, current_balance FROM customers WHERE vendor_id = ? ORDER BY name ASC");
$stmt->execute([$user_id]);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selected_customer_id = $_GET['customer_id'] ?? '';
?>

<div class="page-content">
<div class="card card-form">
    <h2>Record Debt Payment</h2>
    <?php
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-error">' . htmlspecialchars($_GET['error']) . '</div>';
    }
    ?>
    <form action="../../actions/payment_action.php" method="POST">
        <input type="hidden" name="action" value="record_payment">
        
        <div class="form-group">
            <label for="customer_id">Select Customer</label>
            <select name="customer_id" id="customer_id" class="form-control" required onchange="updateBalanceHint()">
                <option value="">-- Select Customer --</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?php echo $customer['customer_id']; ?>" 
                            data-balance="<?php echo $customer['current_balance']; ?>"
                            <?php echo ($selected_customer_id == $customer['customer_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($customer['name']); ?> 
                    </option>
                <?php endforeach; ?>
            </select>
            <small id="balance_hint"></small>
        </div>

        <div class="form-group">
            <label for="amount">Payment Amount (KES)</label>
            <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" required>
        </div>

        <button type="submit" class="btn btn-payment btn-full">Record Payment</button>
    </form>
    <p class="text-center"><a href="../dashboard.php">Cancel</a></p>
</div>
</div>

<script>
    function updateBalanceHint() {
        var select = document.getElementById('customer_id');
        var hint = document.getElementById('balance_hint');
        var selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.value) {
            var balance = selectedOption.getAttribute('data-balance');
            hint.textContent = "Current Debt: KES " + balance;
            // Pre-fill amount? No, let them type it.
        } else {
            hint.textContent = "";
        }
    }
    // Run on load if selected
    window.onload = updateBalanceHint;
</script>

<?php include '../../includes/footer.php'; ?>
