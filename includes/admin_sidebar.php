<?php
if (!isset($base_path)) {
    $base_path = '../';
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="admin-sidebar-overlay" id="adminSidebarOverlay"></div>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar-header" style="display: flex; justify-content: space-between; align-items: center; position: relative;">
        <a href="<?php echo $base_path; ?>admin/index.php" style="color:white;">ALS Admin</a>
        <button id="themeToggleBtn" class="theme-toggle-btn" style="margin-left: 0; padding: 0.25rem; color: rgba(255, 255, 255, 0.8);" aria-label="Toggle Theme">
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
        <button class="hamburger" id="closeSidebar" style="display:none; position:absolute; right:1rem; top:1.5rem;">&times;</button>
    </div>
    <nav class="admin-sidebar-nav">
        <a href="<?php echo $base_path; ?>admin/index.php" class="admin-nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            Dashboard
        </a>
        <a href="<?php echo $base_path; ?>admin/bookings.php" class="admin-nav-link <?php echo $current_page == 'bookings.php' ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Bookings
        </a>
        <a href="<?php echo $base_path; ?>admin/courts.php" class="admin-nav-link <?php echo $current_page == 'courts.php' ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
            Courts
        </a>
        <a href="<?php echo $base_path; ?>admin/users.php" class="admin-nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            Users
        </a>
        <a href="<?php echo $base_path; ?>admin/revenue.php" class="admin-nav-link <?php echo $current_page == 'revenue.php' ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            Revenue
        </a>
        <a href="<?php echo $base_path; ?>admin/messages.php" class="admin-nav-link <?php echo $current_page == 'messages.php' ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            Messages
        </a>
        
        <div style="margin-top:auto; padding: 1rem;">
            <a href="<?php echo $base_path; ?>logout.php" class="btn btn-outline" style="width:100%; border-color: rgba(255,255,255,0.2); color: rgba(255,255,255,0.8);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.5rem;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Logout
            </a>
            <a href="<?php echo $base_path; ?>index.php" class="btn text-secondary" style="width:100%; margin-top: 0.5rem; font-size: 0.875rem;">
                Back to Site
            </a>
        </div>
    </nav>
</aside>