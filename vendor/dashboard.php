<?php
// vendor/dashboard.php

session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireRole('vendor');

$page_title = "Dashboard";
include '../includes/header.php';

$user_id = $_SESSION['user_id'];
$db = new Database();
$conn = $db->getConnection();

// 1. Total Outstanding Debt
$stmt = $conn->prepare("SELECT SUM(current_balance) as total_debt FROM customers WHERE vendor_id = ?");
$stmt->execute([$user_id]);
$total_debt = $stmt->fetch(PDO::FETCH_ASSOC)['total_debt'] ?? 0;

// 2. Top 5 Debtors (ordered by balance DESC)
$stmt = $conn->prepare("SELECT customer_id, name, email, current_balance FROM customers WHERE vendor_id = ? ORDER BY current_balance DESC LIMIT 5");
$stmt->execute([$user_id]);
$top_debtors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Customer Count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM customers WHERE vendor_id = ?");
$stmt->execute([$user_id]);
$customer_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>

<div class="container">
    <h2 class="mt-sm">Vendor Dashboard</h2>
    <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>

    <!-- Metrics Cards -->
    <div class="flex gap-md flex-wrap">
        
        <div class="card card-metric-orange">
            <h3>Total Receivables</h3>
            <p class="balance-large metric-orange">
                KES <?php echo number_format($total_debt, 2); ?>
            </p>
        </div>

        <div class="card card-metric-green">
            <h3>My Customers</h3>
            <p class="balance-large metric-green">
                <?php echo $customer_count; ?>
            </p>
        </div>

        <div class="card card-metric-blue">
            <h3>Quick Actions</h3>
            <div class="flex flex-column gap-sm">
                <a href="customers/add.php" class="btn btn-primary btn-small">Add Customer</a>
                <a href="sales/add.php" class="btn btn-sale btn-small">Record Sale</a>
            </div>
        </div>

    </div>

    <!-- Top Debtors Table -->
    <div class="card">
        <h3>Top 5 Debtors</h3>
        <?php if (count($top_debtors) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-left">Name</th>
                        <th class="text-right">Current Balance</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_debtors as $debtor): ?>
                        <tr>
                            <td>
                                <a href="customers/edit.php?id=<?php echo $debtor['customer_id']; ?>" class="customer-name-link">
                                    <?php echo htmlspecialchars($debtor['name']); ?>
                                </a>
                            </td>
                            <td class="text-right balance-positive">
                                KES <?php echo number_format($debtor['current_balance'], 2); ?>
                            </td>
                            <td class="table-actions">
                                <a href="sales/add.php?customer_id=<?php echo $debtor['customer_id']; ?>" class="action-link action-link-sale" title="Record Sale">
                                    <i class="fas fa-shopping-cart"></i> Sale
                                </a>
                                <a href="payments/add.php?customer_id=<?php echo $debtor['customer_id']; ?>" class="action-link action-link-payment" title="Record Payment">
                                    <i class="fas fa-money-bill"></i> Payment
                                </a>
                                <?php if (!empty($debtor['email'])): ?>
                                    <a href="#" class="action-link action-link-remind remind-single-dashboard" data-customer-id="<?php echo $debtor['customer_id']; ?>" title="Send Reminder">
                                        <i class="fas fa-envelope"></i> Remind
                                    </a>
                                <?php else: ?>
                                    <span class="action-link-disabled" title="No email address - Edit customer to add email">
                                        <i class="fas fa-envelope"></i> Remind
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="mt-sm text-right">
                <a href="customers/index.php">View All Customers &rarr;</a>
            </div>
        <?php else: ?>
            <p class="empty-state">No debtors found. Good job collecting debts!</p>
        <?php endif; ?>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const remindLinks = document.querySelectorAll('.remind-single-dashboard');
    
    remindLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const customerId = this.getAttribute('data-customer-id');
            
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../actions/reminder_action.php';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'send_reminder';
            form.appendChild(actionInput);
            
            const customerInput = document.createElement('input');
            customerInput.type = 'hidden';
            customerInput.name = 'customer_ids[]';
            customerInput.value = customerId;
            form.appendChild(customerInput);
            
            document.body.appendChild(form);
            form.submit();
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>
