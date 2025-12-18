<?php
// includes/navbar.php
// Use $base_url from header.php (based on BASE_PATH env).
?>
<nav class="navbar">
    <a href="<?php echo $base_url; ?>index.php" class="navbar-brand">Mama Mboga</a>
    <div class="nav-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="<?php echo $base_url; ?>admin/dashboard.php">Admin Dashboard</a>
            <?php else: ?>
                <a href="<?php echo $base_url; ?>vendor/dashboard.php">Dashboard</a>
            <?php endif; ?>
            <a href="<?php echo $base_url; ?>logout.php">Logout</a>
            <?php else: ?>
                <a href="<?php echo $base_url; ?>auth.php">Login</a>
                <a href="<?php echo $base_url; ?>auth.php?mode=register">Register</a>
            <?php endif; ?>
    </div>
</nav>
