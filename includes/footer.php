</div> <!-- End Container -->
    <footer style="text-align: center; margin-top: 50px; padding: 20px; color: #777;">
        <p>&copy; <?php echo date('Y'); ?> Mama Mboga. All rights reserved.</p>
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php
    // Determine base path for assets based on current directory
    $asset_base = '';
    $current_dir = dirname($_SERVER['PHP_SELF']);
    if (strpos($current_dir, '/admin') !== false || strpos($current_dir, '/vendor') !== false) {
        $asset_base = '../';
    }
    ?>
    <script src="<?php echo $asset_base; ?>assets/js/main.js"></script>
</body>
</html>
