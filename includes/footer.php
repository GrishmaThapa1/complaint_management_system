<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/complaint_management/');
}
?>

<footer>
    <div class="footer-container">
        <!-- About Section -->
        <div class="footer-about">
            <h3><i class="fas fa-info-circle"></i> About Us</h3>
            <p>We simplify complaint submission, tracking, and resolution online for a smoother communication experience.</p>
        </div>

        <!-- Quick Links Section -->
        <div class="footer-links">
            <h3><i class="fas fa-link"></i> Quick Links</h3>
            <ul>
                <li><a href="<?= BASE_URL ?>index.php">Home</a></li>
                <li><a href="<?= BASE_URL ?>about.php">About Us</a></li>
                <li><a href="<?= BASE_URL ?>contact.php">Contact</a></li>

                <?php if (isset($_SESSION['role'])): ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="<?= BASE_URL ?>admin/dashboard.php">Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="<?= BASE_URL ?>user/dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>user/register.php">Register</a></li>
                    <li><a href="<?= BASE_URL ?>login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Contact Section -->
        <div class="footer-contact">
            <h3><i class="fas fa-phone"></i> Contact Us</h3>
            <p><i class="fas fa-envelope"></i> info@complaintsystem.com</p>
            <p><i class="fas fa-phone"></i> +977 9800000000</p>
            <p><i class="fas fa-map-marker-alt"></i> Kathmandu, Nepal</p>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        &copy; <?= date("Y"); ?> Complaint Management System. All rights reserved.
    </div>
</footer>

<script src="<?= BASE_URL ?>Js/script.js"></script>
</body>

</html>