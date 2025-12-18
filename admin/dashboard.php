<?php
// admin/dashboard.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireRole('admin');

$page_title = "Admin Dashboard";
include '../includes/header.php';

$db = new Database();
$conn = $db->getConnection();

// Metrics
$stmt = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'vendor'");
$vendor_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $conn->query("SELECT COUNT(*) as count FROM customers");
$customer_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $conn->query("SELECT SUM(current_balance) as total_dept FROM customers");
$system_debt = $stmt->fetch(PDO::FETCH_ASSOC)['total_dept'] ?? 0;

// Search term for vendors
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';

// Fetch Vendors
if (!empty($search)) {
    $stmt = $conn->prepare("SELECT user_id, username, email, created_at, is_active FROM users WHERE role = 'vendor' AND (username LIKE ? OR email LIKE ?) ORDER BY created_at DESC");
    $like = '%' . $search . '%';
    $stmt->execute([$like, $like]);
} else {
    $stmt = $conn->prepare("SELECT user_id, username, email, created_at, is_active FROM users WHERE role = 'vendor' ORDER BY created_at DESC");
    $stmt->execute();
}
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2 class="mt-sm">Admin Dashboard</h2>
    
    <!-- Metrics -->
    <div class="flex gap-md mb-md">
        <div class="card card-metric">
            <h3>Total Vendors</h3>
            <p class="balance-large"><?php echo $vendor_count; ?></p>
        </div>
        <div class="card card-metric">
            <h3>Total Customers Managed</h3>
            <p class="balance-large"><?php echo $customer_count; ?></p>
        </div>
        <div class="card card-metric">
            <h3>Total System Debt</h3>
            <p class="balance-large metric-red">KES <?php echo number_format($system_debt, 2); ?></p>
        </div>
    </div>

    <!-- Vendor Management -->
    <div class="card">
        <h3>Manage Vendors</h3>

        <!-- Search Vendors -->
        <form method="GET" action="" class="search-form">
            <div class="search-input-wrapper">
                <input type="text"
                       name="search"
                       id="searchInput"
                       class="form-control"
                       placeholder="Search vendors by username or email..."
                       value="<?php echo htmlspecialchars($search); ?>">
                <i class="fas fa-search search-icon"></i>
            </div>
            <?php if (!empty($search)): ?>
                <a href="dashboard.php" class="btn-secondary" style="white-space: nowrap; flex-shrink: 0;">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
        </form>

        <?php if (!empty($search)): ?>
            <p class="mb-sm" style="color: #666;">
                <i class="fas fa-info-circle"></i>
                Found <?php echo count($vendors); ?> vendor(s) matching "<?php echo htmlspecialchars($search); ?>"
            </p>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-left">Username</th>
                    <th class="text-left">Email</th>
                    <th class="text-left">Joined Date</th>
                    <th class="text-left">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendors as $vendor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($vendor['username']); ?></td>
                        <td><?php echo htmlspecialchars($vendor['email']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($vendor['created_at'])); ?></td>
                        <td>
                            <?php if ((int)$vendor['is_active'] === 1): ?>
                                <span class="metric-green">Active</span>
                            <?php else: ?>
                                <span class="metric-red">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="table-actions">
                                <a href="edit_vendor.php?id=<?php echo $vendor['user_id']; ?>" class="action-link" title="Edit vendor details">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="../actions/admin_vendor_action.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="vendor_id" value="<?php echo $vendor['user_id']; ?>">
                                    <?php if ((int)$vendor['is_active'] === 1): ?>
                                        <button type="submit" class="btn btn-small" style="background-color: #e74c3c; color: #fff; border: none;">
                                            Deactivate
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-small" style="background-color: #27ae60; color: #fff; border: none;">
                                            Activate
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
