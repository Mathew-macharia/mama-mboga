<?php
// includes/navbar.php
// Determine base path based on current directory
$base_path = '';
$current_dir = dirname($_SERVER['PHP_SELF']);
if (strpos($current_dir, '/admin') !== false || strpos($current_dir, '/vendor') !== false) {
    $base_path = '../';
}
?>
<nav class="navbar">
    <a href="<?php echo $base_path; ?>index.php" class="navbar-brand">Mama Mboga</a>
    <div class="nav-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="<?php echo $base_path; ?>admin/dashboard.php">Admin Dashboard</a>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>vendor/dashboard.php">Dashboard</a>
            <?php endif; ?>
            <a href="<?php echo $base_path; ?>logout.php">Logout</a>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>auth.php">Login</a>
                <a href="<?php echo $base_path; ?>auth.php?mode=register">Register</a>
            <?php endif; ?>
    </div>
</nav>
