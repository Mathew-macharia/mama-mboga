<?php
// vendor/customers/add.php
session_start();
require_once '../../includes/functions.php';
requireRole('vendor');

$page_title = "Add Customer";
include '../../includes/header.php';
?>

<div class="page-content">
<div class="card card-form">
    <h2>Add New Customer</h2>
    <?php
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-error">' . htmlspecialchars($_GET['error']) . '</div>';
    }
    ?>
    <form action="../../actions/customer_action.php" method="POST">
        <input type="hidden" name="action" value="add_customer">
        
        <div class="form-group">
            <label for="name">Customer Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address (Optional)</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="customer@example.com">
            <small>Required for sending payment reminders</small>
        </div>

        <button type="submit" class="btn btn-primary btn-full">Add Customer</button>
    </form>
    <p class="text-center"><a href="index.php">Cancel</a></p>
</div>
</div>

<?php include '../../includes/footer.php'; ?>
