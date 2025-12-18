<?php
// vendor/customers/index.php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

requireRole('vendor');

$page_title = "My Customers";
include '../../includes/header.php';

$user_id = $_SESSION['user_id'];
$db = new Database();
$conn = $db->getConnection();

// Get search term
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';

// Build query with search filter
$sql = "SELECT * FROM customers WHERE vendor_id = ?";
$params = [$user_id];

if (!empty($search)) {
    $sql .= " AND name LIKE ?";
    $params[] = '%' . $search . '%';
}

$sql .= " ORDER BY current_balance DESC, created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="page-header">
        <h2>My Customers</h2>
        <a href="add.php" class="btn btn-primary">Add New Customer</a>
    </div>
    
    <div class="card mt-sm">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <!-- Search Form -->
        <form method="GET" action="" class="search-form">
            <div class="search-input-wrapper">
                <input type="text" name="search" id="searchInput" class="form-control" 
                       placeholder="Search customers by name..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <i class="fas fa-search search-icon"></i>
            </div>
            <?php if (!empty($search)): ?>
                <a href="index.php" class="btn-secondary" style="white-space: nowrap; flex-shrink: 0;">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
        </form>

        <?php if (count($customers) > 0): ?>
            <?php if (!empty($search)): ?>
                <p class="mb-sm" style="color: #666;">
                    <i class="fas fa-info-circle"></i> Found <?php echo count($customers); ?> customer(s) matching "<?php echo htmlspecialchars($search); ?>"
                </p>
            <?php endif; ?>
            <form id="reminderForm" action="../../actions/reminder_action.php" method="POST">
                <input type="hidden" name="action" value="send_reminder">
                
                <div class="selection-controls">
                    <div>
                        <label class="checkbox-label">
                            <input type="checkbox" id="selectAll"> Select All
                        </label>
                    </div>
                    <button type="submit" id="remindBtn" class="btn btn-remind" style="display: none;">
                        <i class="fas fa-envelope" style="margin-right: 5px;"></i> Remind Selected
                    </button>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="text-left" style="width: 40px;">
                                <input type="checkbox" id="selectAllHeader">
                            </th>
                            <th class="text-left">Name</th>
                            <th class="text-right">Current Balance</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="customer_ids[]" value="<?php echo $customer['customer_id']; ?>" class="customer-checkbox">
                                </td>
                                <td>
                                    <a href="edit.php?id=<?php echo $customer['customer_id']; ?>" class="customer-name-link">
                                        <?php echo htmlspecialchars($customer['name']); ?>
                                    </a>
                                </td>
                                <td class="text-right <?php echo $customer['current_balance'] > 0 ? 'balance-positive' : 'balance-zero'; ?>">
                                    KES <?php echo number_format($customer['current_balance'], 2); ?>
                                </td>
                                <td class="table-actions">
                                    <a href="../sales/add.php?customer_id=<?php echo $customer['customer_id']; ?>" class="action-link action-link-sale" title="Record Sale">
                                        <i class="fas fa-shopping-cart"></i> Sale
                                    </a>
                                    <a href="../payments/add.php?customer_id=<?php echo $customer['customer_id']; ?>" class="action-link action-link-payment" title="Record Payment">
                                        <i class="fas fa-money-bill"></i> Payment
                                    </a>
                                    <?php if (!empty($customer['email'])): ?>
                                        <a href="#" class="action-link action-link-remind remind-single" data-customer-id="<?php echo $customer['customer_id']; ?>" title="Send Reminder">
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
            </form>
        <?php else: ?>
            <p class="empty-state">
                <?php if (!empty($search)): ?>
                    No customers found matching "<?php echo htmlspecialchars($search); ?>". 
                    <a href="index.php" class="empty-state-link">View all customers</a>
                <?php else: ?>
                    You haven't added any customers yet.
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>
    <p><a href="../dashboard.php">&larr; Back to Dashboard</a></p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllHeader = document.getElementById('selectAllHeader');
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.customer-checkbox');
    const remindBtn = document.getElementById('remindBtn');
    const remindSingleLinks = document.querySelectorAll('.remind-single');
    
    // Select all functionality
    function updateSelectAll() {
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        const someChecked = Array.from(checkboxes).some(cb => cb.checked);
        
        if (selectAllHeader) selectAllHeader.checked = allChecked;
        if (selectAll) selectAll.checked = allChecked;
        
        remindBtn.style.display = someChecked ? 'inline-block' : 'none';
    }
    
    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateSelectAll();
        });
    }
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateSelectAll();
        });
    }
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectAll);
    });
    
    // Individual remind links
    remindSingleLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const customerId = this.getAttribute('data-customer-id');
            
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../actions/reminder_action.php';
            
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
    
    updateSelectAll();
    
    // Search functionality - auto-submit on Enter or after typing stops
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            clearTimeout(searchTimeout);
            
            // Submit on Enter
            if (e.key === 'Enter') {
                this.form.submit();
                return;
            }
            
            // Auto-submit after 500ms of no typing (debounce)
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 2 || this.value.length === 0) {
                    this.form.submit();
                }
            }, 500);
        });
        
        // Focus search input on page load if empty
        if (!searchInput.value) {
            searchInput.focus();
        }
    }
});
</script>

<?php include '../../includes/footer.php'; ?>
