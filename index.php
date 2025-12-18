<?php
// index.php
session_start();
require_once 'includes/functions.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        redirect('admin/dashboard.php');
    } else {
        redirect('vendor/dashboard.php');
    }
}

$page_title = "Welcome";
$hide_container = true; // Prevent header from opening a container
include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="hero-section" style="
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.7)), url('assets/images/mama-mboga.webp');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #fff;
    margin-top: 0; /* Navbar is now floating, no offset needed */
">
    <div class="container">
        <h1 style="font-size: 3.5rem; font-weight: 700; margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
            Mama Mboga
        </h1>
        <p style="font-size: 1.5rem; margin-bottom: 40px; max-width: 800px; margin-left: auto; margin-right: auto; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
            Digitize your credit sales. Manage your customers. <br>Stop profit leakage today.
        </p>
        
        <div style="display: flex; justify-content: center; gap: 20px;">
            <a href="auth.php?mode=register" class="btn btn-primary" style="
                padding: 15px 40px; 
                font-size: 1.2rem; 
                background-color: #27ae60; 
                border: none; 
                border-radius: 30px; 
                box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);
                transition: transform 0.2s;
            ">Get Started</a>
            <a href="auth.php" class="btn" style="
                background: transparent; 
                border: 2px solid #fff; 
                color: #fff; 
                padding: 15px 40px; 
                font-size: 1.2rem;
                border-radius: 30px;
                transition: background 0.2s;
            ">Login</a>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="features-wrapper">
<div class="container" style="padding: 100px 20px;">
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; max-width: 1200px; margin: 0 auto;">
        <!-- Feature 1 -->
        <div class="feature-card" style="
            flex: 1;
            min-width: 280px;
            padding: 40px 30px; 
            background: #fff; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: left;
        ">
            <div style="
                width: 60px; 
                height: 60px; 
                background: rgba(39, 174, 96, 0.1); 
                border-radius: 12px; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                margin-bottom: 25px;
            ">
                <i class="fas fa-chart-line" style="font-size: 1.8rem; color: #27ae60;"></i>
            </div>
            <h3 style="color: #2c3e50; margin-bottom: 15px; font-size: 1.5rem;">Track Debt</h3>
            <p style="color: #7f8c8d; line-height: 1.6;">Eliminate the chaos of paper notebooks. Record every credit sale instantly and know exactly who owes you what at a glance.</p>
        </div>

        <!-- Feature 2 -->
        <div class="feature-card" style="
            flex: 1;
            min-width: 280px;
            padding: 40px 30px; 
            background: #fff; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: left;
        ">
            <div style="
                width: 60px; 
                height: 60px; 
                background: rgba(230, 126, 34, 0.1); 
                border-radius: 12px; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                margin-bottom: 25px;
            ">
                <i class="fas fa-users" style="font-size: 1.8rem; color: #e67e22;"></i>
            </div>
            <h3 style="color: #2c3e50; margin-bottom: 15px; font-size: 1.5rem;">Manage Customers</h3>
            <p style="color: #7f8c8d; line-height: 1.6;">Maintain a secure digital phonebook. Access customer details instantly and build stronger relationships with your loyal buyers.</p>
        </div>

        <!-- Feature 3 -->
        <div class="feature-card" style="
            flex: 1;
            min-width: 280px;
            padding: 40px 30px; 
            background: #fff; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: left;
        ">
            <div style="
                width: 60px; 
                height: 60px; 
                background: rgba(41, 128, 185, 0.1); 
                border-radius: 12px; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                margin-bottom: 25px;
            ">
                <i class="fas fa-wallet" style="font-size: 1.8rem; color: #2980b9;"></i>
            </div>
            <h3 style="color: #2c3e50; margin-bottom: 15px; font-size: 1.5rem;">Grow Profits</h3>
            <p style="color: #7f8c8d; line-height: 1.6;">Stop losing money to forgotten debts. Identify your top customers and improve your cash flow collection process.</p>
        </div>
    </div>
</div>
</div>

<style>
/* Hover effect removed as per request */
.feature-card {
    /* Base styles are inline */
}
</style>

</div> <!-- Close Features Container -->

<!-- Floating Background Section (Desktop Only) -->
<div class="floating-background-section"></div>

<?php include 'includes/footer.php'; ?>
