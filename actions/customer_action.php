<?php
// actions/customer_action.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireRole('vendor');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    
    $action = $_POST['action'] ?? '';

    if ($action === 'add_customer') {
        $name = clean_input($_POST['name']);
        $phone = clean_input($_POST['phone']);
        $email = !empty($_POST['email']) ? clean_input($_POST['email']) : null;
        $user_id = $_SESSION['user_id'];

        if (empty($name) || empty($phone)) {
            redirect("../vendor/customers/add.php?error=Name and phone are required");
        }

        // Validate email if provided
        if ($email && !validateEmail($email)) {
            redirect("../vendor/customers/add.php?error=Invalid email address");
        }

        $stmt = $conn->prepare("INSERT INTO customers (vendor_id, name, phone, email) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$user_id, $name, $phone, $email])) {
            redirect("../vendor/customers/index.php?success=Customer added successfully");
        } else {
            redirect("../vendor/customers/add.php?error=Failed to add customer");
        }
    } elseif ($action === 'edit_customer') {
        $customer_id = $_POST['customer_id'] ?? 0;
        $name = clean_input($_POST['name']);
        $phone = clean_input($_POST['phone']);
        $email = !empty($_POST['email']) ? clean_input($_POST['email']) : null;
        $user_id = $_SESSION['user_id'];

        if (empty($name) || empty($phone)) {
            redirect("../vendor/customers/edit.php?id=$customer_id&error=Name and phone are required");
        }

        // Validate email if provided
        if ($email && !validateEmail($email)) {
            redirect("../vendor/customers/edit.php?id=$customer_id&error=Invalid email address");
        }

        // Verify customer belongs to this vendor
        $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE customer_id = ? AND vendor_id = ?");
        $stmt->execute([$customer_id, $user_id]);
        
        if ($stmt->rowCount() === 0) {
            redirect("../vendor/customers/index.php?error=Customer not found");
        }

        // Update customer
        $stmt = $conn->prepare("UPDATE customers SET name = ?, phone = ?, email = ? WHERE customer_id = ? AND vendor_id = ?");
        
        if ($stmt->execute([$name, $phone, $email, $customer_id, $user_id])) {
            redirect("../vendor/customers/index.php?success=Customer updated successfully");
        } else {
            redirect("../vendor/customers/edit.php?id=$customer_id&error=Failed to update customer");
        }
    }
} else {
    redirect("../vendor/dashboard.php");
}
?>
