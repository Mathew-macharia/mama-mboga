<?php
// admin/edit_vendor.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireRole('admin');

$page_title = "Edit Vendor";
include '../includes/header.php';

$db = new Database();
$conn = $db->getConnection();

// Get vendor ID
$vendor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch vendor details (only vendors, not admins)
$stmt = $conn->prepare("SELECT user_id, username, email, is_active FROM users WHERE user_id = ? AND role = 'vendor'");
$stmt->execute([$vendor_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
    redirect("dashboard.php?error=" . urlencode('Vendor not found'));
}
?>

<div class="page-content">
<div class="card card-form">
    <h2>Edit Vendor</h2>
    <?php
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-error">' . htmlspecialchars($_GET['error']) . '</div>';
    }
    if (isset($_GET['success'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
    }
    ?>
    <form action="../actions/admin_vendor_action.php" method="POST">
        <input type="hidden" name="action" value="update_vendor">
        <input type="hidden" name="vendor_id" value="<?php echo $vendor['user_id']; ?>">
        
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control"
                   value="<?php echo htmlspecialchars($vendor['username']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control"
                   value="<?php echo htmlspecialchars($vendor['email']); ?>" required>
        </div>

        <div class="form-group">
            <label>Status</label>
            <input type="text" class="form-control input-readonly"
                   value="<?php echo (int)$vendor['is_active'] === 1 ? 'Active' : 'Inactive'; ?>"
                   readonly>
            <small>Status can be toggled on the Admin Dashboard.</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Update Vendor</button>
            <a href="dashboard.php" class="btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
        </div>
    </form>
</div>
</div>

<?php include '../includes/footer.php'; ?>


