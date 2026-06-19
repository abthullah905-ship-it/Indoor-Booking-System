<?php
if (!isset($base_path)) {
    $base_path = '';
}
?>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h3 class="footer-title">ALS Indoor</h3>
                <p>Your ultimate destination for indoor sports. We offer premium quality courts for Football, Cricket, and Badminton with professional lighting and facilities.</p>
                <div class="social-icons" style="margin-top: 1.5rem;">
                    <a href="#" aria-label="Facebook">
                        <svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path></svg>
                    </a>
                    <a href="#" aria-label="Instagram">
                        <svg viewBox="0 0 24 24"><path d="M12 2c2.717 0 3.056.01 4.122.06 1.065.05 1.79.217 2.428.465.66.254 1.216.598 1.772 1.153a4.908 4.908 0 011.153 1.772c.247.637.415 1.363.465 2.428.047 1.066.06 1.405.06 4.122 0 2.717-.01 3.056-.06 4.122-.05 1.065-.218 1.79-.465 2.428a4.883 4.883 0 01-1.153 1.772 4.915 4.915 0 01-1.772 1.153c-.637.247-1.363.415-2.428.465-1.066.047-1.405.06-4.122.06-2.717 0-3.056-.01-4.122-.06-1.065-.05-1.79-.218-2.428-.465a4.89 4.89 0 01-1.772-1.153 4.904 4.904 0 01-1.153-1.772c-.248-.637-.415-1.363-.465-2.428C2.013 15.056 2 14.717 2 12c0-2.717.01-3.056.06-4.122.05-1.066.217-1.79.465-2.428a4.88 4.88 0 011.153-1.772A4.897 4.897 0 015.45 2.525c.638-.248 1.362-.415 2.428-.465C8.944 2.013 9.283 2 12 2zm0 5a5 5 0 100 10 5 5 0 000-10zm6.5-.25a1.25 1.25 0 00-2.5 0 1.25 1.25 0 002.5 0zM12 9a3 3 0 110 6 3 3 0 010-6z"></path></svg>
                    </a>
                    <a href="#" aria-label="Twitter">
                        <svg viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path></svg>
                    </a>
                </div>
            </div>

            <div class="footer-col">
                <h3 class="footer-title">Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo $base_path; ?>index.php">Home</a></li>
                    <li><a href="<?php echo $base_path; ?>booking.php">Book Court</a></li>
                    <li><a href="<?php echo $base_path; ?>about.php">About Us</a></li>
                    <li><a href="<?php echo $base_path; ?>contact.php">Contact Us</a></li>
                    <li><a href="<?php echo $base_path; ?>admin/login.php">Admin Login</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3 class="footer-title">Contact Us</h3>
                <ul class="footer-links">
                    <li>Akkaraipattu 16</li>
                    <li>Phone: +94 75 952 2543</li>
                    <li>Email: ijasahamed905@gmail.com</li>
                </ul>
            </div>

            <div class="footer-col">
                <h3 class="footer-title">Opening Hours</h3>
                <ul class="footer-links">
                    <li>Monday - Friday: 6 AM - 11 PM</li>
                    <li>Saturday - Sunday: 6 AM - 01 AM</li>
                    <li>Open all year round</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> ALS Indoor Sports. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="<?php echo $base_path; ?>assets/js/main.js"></script>
</body>
</html>
