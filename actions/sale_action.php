<?php
// actions/sale_action.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireRole('vendor');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    
    $action = $_POST['action'] ?? '';

    if ($action === 'record_sale') {
        $customer_id = $_POST['customer_id'];
        $item_name = clean_input($_POST['item_name']);
        $quantity = (int)$_POST['quantity'];
        $price = (float)$_POST['price_per_unit'];
        $total_amount = $quantity * $price; // Recalculate server-side for safety
        $user_id = $_SESSION['user_id'];

        if (empty($customer_id) || empty($item_name) || $total_amount <= 0) {
            redirect("../vendor/sales/add.php?error=Invalid Input");
        }

        try {
            $conn->beginTransaction();

            // 1. Insert Sale
            $stmt = $conn->prepare("INSERT INTO sales (customer_id, vendor_id, item_name, quantity, price_per_unit, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$customer_id, $user_id, $item_name, $quantity, $price, $total_amount]);

            // 2. Update Customer Balance
            $stmt = $conn->prepare("UPDATE customers SET current_balance = current_balance + ? WHERE customer_id = ?");
            $stmt->execute([$total_amount, $customer_id]);

            $conn->commit();
            redirect("../vendor/customers/index.php?success=Sale recorded and balance updated!");

        } catch (Exception $e) {
            $conn->rollBack();
            redirect("../vendor/sales/add.php?error=Transaction failed: " . $e->getMessage());
        }
    }
} else {
    redirect("../vendor/dashboard.php");
}
?>
