<?php
// includes/functions.php

function clean_input($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = clean_input($value);
        }
        return $data;
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireRole($role) {
    if (!isLoggedIn() || $_SESSION['role'] !== $role) {
        redirect('../index.php');
    }
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function formatCurrency($amount) {
    return 'KES ' . number_format($amount, 2);
}
?>
