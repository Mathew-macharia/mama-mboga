<?php
// vendor/customers/edit.php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

requireRole('vendor');

$page_title = "Edit Customer";
include '../../includes/header.php';

$user_id = $_SESSION['user_id'];
$db = new Database();
$conn = $db->getConnection();

// Get customer ID
$customer_id = $_GET['id'] ?? 0;

// Fetch customer details
$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ? AND vendor_id = ?");
$stmt->execute([$customer_id, $user_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    redirect("index.php?error=Customer not found");
}
?>

<div class="page-content">
<div class="card card-form">
    <h2>Edit Customer</h2>
    <?php
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-error">' . htmlspecialchars($_GET['error']) . '</div>';
    }
    if (isset($_GET['success'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
    }
    ?>
    <form action="../../actions/customer_action.php" method="POST">
        <input type="hidden" name="action" value="edit_customer">
        <input type="hidden" name="customer_id" value="<?php echo $customer['customer_id']; ?>">
        
        <div class="form-group">
            <label for="name">Customer Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($customer['email'] ?? ''); ?>" placeholder="customer@example.com">
            <small>Required for sending payment reminders</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Update Customer</button>
            <a href="index.php" class="btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
        </div>
    </form>
</div>
</div>

<?php include '../../includes/footer.php'; ?>

