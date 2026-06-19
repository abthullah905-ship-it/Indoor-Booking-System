<?php
// Ensure $base_path is set (defaults to empty string if not defined)
if (!isset($base_path)) {
    $base_path = '';
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="<?php echo $base_path; ?>index.php" class="navbar-brand">
            <img src="<?php echo $base_path; ?>uploads/nav_logo1.png" width="50" height="50" alt="ALS Indoor Logo" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'40\' height=\'40\'><rect width=\'40\' height=\'40\' fill=\'%235cb85c\'/><text x=\'50%\' y=\'50%\' fill=\'white\' font-family=\'sans-serif\' font-size=\'16\' text-anchor=\'middle\' dominant-baseline=\'middle\'>ALS</text></svg>'">
            ALS Indoor
        </a>
 
        <!-- Hamburger Menu -->
        <button class="hamburger" id="navToggle" aria-label="Toggle Navigation">
            <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                <path fill-rule="evenodd" d="M3 5h18v2H3V5zm0 6h18v2H3v-2zm0 6h18v2H3v-2z" clip-rule="evenodd"></path>
            </svg>
        </button>

        <div class="nav-overlay" id="navOverlay"></div>

        <div class="nav-links" id="navLinks">
            <div class="nav-mobile-header">
                <span class="nav-mobile-title">Menu</span>
                <button class="hamburger" id="closeNav" aria-label="Close Navigation">&times;</button>
            </div>

            <a href="<?php echo $base_path; ?>index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a>
            <a href="<?php echo $base_path; ?>booking.php" class="nav-link <?php echo $current_page == 'booking.php' ? 'active' : ''; ?>">Book Now</a>
            <a href="<?php echo $base_path; ?>about.php" class="nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About Us</a>
            <a href="<?php echo $base_path; ?>contact.php" class="nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact Us</a>
            
            <button id="themeToggleBtn" class="theme-toggle-btn" aria-label="Toggle Theme">
                <span id="themeIconContainer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                </span>
            </button>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $base_path; ?>profile.php" class="nav-user">
                    Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                </a>
                <a href="<?php echo $base_path; ?>logout.php" class="btn btn-danger" style="padding: 0.4rem 1rem;">Logout</a>
            <?php else: ?>
                <a href="#" onclick="openModal('auth-modal'); return false;" class="btn btn-primary" style="padding: 0.4rem 1rem;">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
