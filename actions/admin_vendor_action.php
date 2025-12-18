<?php
// actions/admin_vendor_action.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../admin/dashboard.php');
}

$action = $_POST['action'] ?? '';
$vendor_id = isset($_POST['vendor_id']) ? (int)$_POST['vendor_id'] : 0;

$db = new Database();
$conn = $db->getConnection();

if ($action === 'toggle_status' && $vendor_id > 0) {
    // Toggle is_active for a vendor (but never allow changing admins)
    $stmt = $conn->prepare("SELECT is_active FROM users WHERE user_id = ? AND role = 'vendor'");
    $stmt->execute([$vendor_id]);
    $vendor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vendor) {
        redirect('../admin/dashboard.php?error=Vendor not found');
    }

    $newStatus = (int)$vendor['is_active'] === 1 ? 0 : 1;

    $stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE user_id = ? AND role = 'vendor'");
    $stmt->execute([$newStatus, $vendor_id]);

    $message = $newStatus === 1 ? 'Vendor account activated' : 'Vendor account deactivated';
    redirect('../admin/dashboard.php?success=' . urlencode($message));

} elseif ($action === 'update_vendor' && $vendor_id > 0) {
    // Update vendor username/email only
    $username = clean_input($_POST['username'] ?? '');
    $email = clean_input($_POST['email'] ?? '');

    if (empty($username) || empty($email)) {
        redirect("../admin/edit_vendor.php?id={$vendor_id}&error=" . urlencode('Username and Email are required'));
    }

    if (!validateEmail($email)) {
        redirect("../admin/edit_vendor.php?id={$vendor_id}&error=" . urlencode('Invalid email address'));
    }

    // Ensure the vendor exists and is a vendor
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? AND role = 'vendor'");
    $stmt->execute([$vendor_id]);
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        redirect('../admin/dashboard.php?error=Vendor not found');
    }

    // Check for username/email uniqueness (excluding this vendor)
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
    $stmt->execute([$username, $email, $vendor_id]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        redirect("../admin/edit_vendor.php?id={$vendor_id}&error=" . urlencode('Username or Email already in use'));
    }

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ? AND role = 'vendor'");
    if ($stmt->execute([$username, $email, $vendor_id])) {
        redirect("../admin/edit_vendor.php?id={$vendor_id}&success=" . urlencode('Vendor details updated'));
    } else {
        redirect("../admin/edit_vendor.php?id={$vendor_id}&error=" . urlencode('Failed to update vendor'));
    }
}

// Fallback
redirect('../admin/dashboard.php');


