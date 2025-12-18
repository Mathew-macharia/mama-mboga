<?php
// actions/auth_action.php

// Temporary: enable detailed error reporting to diagnose 500 errors.
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        // Registration Logic
        $username = clean_input($_POST['username']);
        $email = clean_input($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            redirect("../auth.php?mode=register&error=All fields are required");
        }

        if ($password !== $confirm_password) {
            redirect("../auth.php?mode=register&error=Passwords do not match");
        }

        // Strong Password Regex: 8+ chars, at least 1 uppercase, 1 lowercase, 1 number, 1 special char
        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $password)) {
            redirect("../auth.php?mode=register&error=Password must be at least 8 characters long, contain uppercase, lowercase, number, and special character.");
        }

        // Check if user exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        
        if ($stmt->rowCount() > 0) {
            redirect("../auth.php?mode=register&error=Username or Email already exists");
        }

        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert User
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'vendor')");
        
        if ($stmt->execute([$username, $email, $password_hash])) {
            redirect("../auth.php?mode=login&success=Registration successful! Please login.");
        } else {
            redirect("../auth.php?mode=register&error=Registration failed. Please try again.");
        }

    } elseif ($action === 'login') {
        // Login Logic
        $identifier = clean_input($_POST['identifier']); // Username or Email
        $password = $_POST['password'];

        if (empty($identifier) || empty($password)) {
            redirect("../auth.php?mode=login&error=All fields are required");
        }

        $stmt = $conn->prepare("SELECT user_id, username, password_hash, role FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Success
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                redirect("../admin/dashboard.php");
            } else {
                redirect("../vendor/dashboard.php");
            }
        } else {
            redirect("../auth.php?mode=login&error=Invalid credentials");
        }
    }
} else {
    redirect("../index.php");
}
?>
