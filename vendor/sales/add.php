<?php
// vendor/sales/add.php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

requireRole('vendor');

$page_title = "Record Credit Sale";
include '../../includes/header.php';

$user_id = $_SESSION['user_id'];
$db = new Database();
$conn = $db->getConnection();

// Fetch Customers for Dropdown
$stmt = $conn->prepare("SELECT customer_id, name FROM customers WHERE vendor_id = ? ORDER BY name ASC");
$stmt->execute([$user_id]);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selected_customer_id = $_GET['customer_id'] ?? '';
?>

<div class="page-content">
<div class="card card-form-wide">
    <h2>Record New Credit Sale</h2>
    <?php
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-error">' . htmlspecialchars($_GET['error']) . '</div>';
    }
    ?>
    <form action="../../actions/sale_action.php" method="POST">
        <input type="hidden" name="action" value="record_sale">
        
        <div class="form-group">
            <label for="customer_id">Select Customer</label>
            <select name="customer_id" id="customer_id" class="form-control" required>
                <option value="">-- Select Customer --</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?php echo $customer['customer_id']; ?>" <?php echo ($selected_customer_id == $customer['customer_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($customer['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="item_name">Item Description</label>
            <input type="text" name="item_name" id="item_name" class="form-control" placeholder="e.g., 2kg Sukuma Wiki" required>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" required>
        </div>

        <div class="form-group">
            <label for="price_per_unit">Price Per Unit (KES)</label>
            <input type="number" name="price_per_unit" id="price_per_unit" class="form-control" step="0.01" min="0" required>
        </div>

        <div class="form-group">
            <label for="total_amount">Total Amount (Auto-calculated)</label>
            <input type="number" name="total_amount" id="total_amount" class="form-control input-readonly" step="0.01" readonly>
        </div>

        <button type="submit" class="btn btn-sale btn-full">Record Sale</button>
    </form>
    <p class="text-center"><a href="../dashboard.php">Cancel</a></p>
</div>
</div>

<script>
    // Simple auto-calculation
    const quantity = document.getElementById('quantity');
    const price = document.getElementById('price_per_unit');
    const total = document.getElementById('total_amount');

    function calculateTotal() {
        const q = parseFloat(quantity.value) || 0;
        const p = parseFloat(price.value) || 0;
        total.value = (q * p).toFixed(2);
    }

    quantity.addEventListener('input', calculateTotal);
    price.addEventListener('input', calculateTotal);
</script>

<?php include '../../includes/footer.php'; ?>
