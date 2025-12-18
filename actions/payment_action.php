<?php
// actions/payment_action.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireRole('vendor');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    
    $action = $_POST['action'] ?? '';

    if ($action === 'record_payment') {
        $customer_id = $_POST['customer_id'];
        $amount = (float)$_POST['amount'];
        $user_id = $_SESSION['user_id'];

        if (empty($customer_id) || $amount <= 0) {
            redirect("../vendor/payments/add.php?error=Invalid Amount or Customer");
        }

        try {
            $conn->beginTransaction();

            // 1. Insert Payment
            $stmt = $conn->prepare("INSERT INTO payments (customer_id, vendor_id, amount) VALUES (?, ?, ?)");
            $stmt->execute([$customer_id, $user_id, $amount]);

            // 2. Reduce Customer Balance (Debt)
            $stmt = $conn->prepare("UPDATE customers SET current_balance = current_balance - ? WHERE customer_id = ?");
            $stmt->execute([$amount, $customer_id]);

            $conn->commit();
            redirect("../vendor/customers/index.php?success=Payment recorded and balance updated!");

        } catch (Exception $e) {
            $conn->rollBack();
            redirect("../vendor/payments/add.php?error=Transaction failed: " . $e->getMessage());
        }
    }
} else {
    redirect("../vendor/dashboard.php");
}
?>
