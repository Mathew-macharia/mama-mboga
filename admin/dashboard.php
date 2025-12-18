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

// Fetch Variables
$stmt = $conn->prepare("SELECT user_id, username, email, created_at FROM users WHERE role = 'vendor' ORDER BY created_at DESC");
$stmt->execute();
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
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-left">Username</th>
                    <th class="text-left">Email</th>
                    <th class="text-left">Joined Date</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendors as $vendor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($vendor['username']); ?></td>
                        <td><?php echo htmlspecialchars($vendor['email']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($vendor['created_at'])); ?></td>
                        <td class="text-center">
                            <!-- In a real app, use a proper form for delete with CSRF token -->
                            <a href="#" style="color: red; opacity: 0.5; cursor: not-allowed;" title="Deletion not implemented for safety">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
