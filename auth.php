<?php
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

$page_title = "Login / Register";
$hide_container = true;
include 'includes/header.php';

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$mode = $_GET['mode'] ?? 'login'; // Default to login mode
?>

<div class="auth-page">
    <div class="container">
        <div class="flip-card-container">
            <div class="flip-card <?php echo $mode === 'register' ? 'flipped' : ''; ?>" id="flipCard">
                <!-- Login Side (Front) -->
                <div class="flip-card-front">
                    <h2 style="color: #fff; margin-bottom: 20px;">Login</h2>
                    <?php if ($error && $mode === 'login'): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    <form action="actions/auth_action.php" method="POST">
                        <input type="hidden" name="action" value="login">
                        <div class="auth-form-group">
                            <input type="text" name="identifier" class="auth-form-control" placeholder="Username or Email" required>
                        </div>
                        <div class="auth-form-group" style="margin-bottom: 20px;">
                            <input type="password" name="password" class="auth-form-control" placeholder="Password" required>
                        </div>
                        <button type="submit" class="auth-btn">Login</button>
                    </form>
                    <p style="margin-top: 20px; color: #fff;">
                        Don't have an account? 
                        <a href="#" class="auth-link" onclick="flipCard(); return false;">Register here</a>
                    </p>
                </div>

                <!-- Register Side (Back) -->
                <div class="flip-card-back">
                    <h2 style="color: #fff; margin-bottom: 20px;">Register</h2>
                    <?php if ($error && $mode === 'register'): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <form action="actions/auth_action.php" method="POST">
                        <input type="hidden" name="action" value="register">
                        <div class="auth-form-group">
                            <input type="text" name="username" class="auth-form-control" placeholder="Username" required>
                        </div>
                        <div class="auth-form-group">
                            <input type="email" name="email" class="auth-form-control" placeholder="Email" required>
                        </div>
                        <div class="auth-form-group">
                            <input type="password" name="password" id="password" class="auth-form-control" placeholder="Password" required>
                            <div id="password-strength" style="margin-top: 5px; font-size: 0.8rem; text-align: left;"></div>
                        </div>
                        <div class="auth-form-group" style="margin-bottom: 20px;">
                            <input type="password" name="confirm_password" id="confirm_password" class="auth-form-control" placeholder="Confirm Password" required>
                        </div>
                        <button type="submit" class="auth-btn">Register</button>
                    </form>
                    <p style="margin-top: 20px; color: #fff;">
                        Already have an account? 
                        <a href="#" class="auth-link" onclick="flipCard(); return false;">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function flipCard() {
        const flipCard = document.getElementById('flipCard');
        flipCard.classList.toggle('flipped');
    }

    // Password strength checker for register form
    document.addEventListener("DOMContentLoaded", function() {
        const passwordInput = document.getElementById("password");
        if (passwordInput) {
            const passwordStrength = document.getElementById("password-strength");

            passwordInput.addEventListener("keyup", function() {
                const password = passwordInput.value;
                const strength = checkPasswordStrength(password);
                passwordStrength.innerHTML = strength;
            });

            function checkPasswordStrength(password) {
                // At least 8 characters, at least one uppercase, one lowercase, one number, one special character
                const strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[\\W_]).{8,}$");
                const mediumRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d|.*[\\W_]).{6,}$");

                if (strongRegex.test(password)) {
                    return '<span style="color: green;">✓ Strong password</span>';
                } else if (mediumRegex.test(password)) {
                    return '<span style="color: orange;">⚠ Medium password</span>';
                } else {
                    return '<span style="color: red;">✕ Weak password</span>';
                }
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>


