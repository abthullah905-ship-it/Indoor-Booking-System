<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<div class="modal-overlay" id="auth-modal">
    <div class="modal-content auth-modal">
        <div class="auth-banner" style="background-image: url('uploads/ij_cric.png');">
            <div class="auth-banner-content">
                <h2 style="color:white; margin-bottom: 1rem;">Join ALS Indoor</h2>
                <p>Book your favorite courts, manage your profile, and start playing with ease.</p>
            </div>
        </div>
        <div class="auth-forms">
            <button class="modal-close" aria-label="Close" style="position: absolute; top: 1rem; right: 1rem;">&times;</button>
            
            <!-- Login Form -->
            <div id="login-form">
                <h3 class="mb-4">Welcome Back</h3>
                <form onsubmit="handleAuth(event, 'login')">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Login</button>
                </form>
                <div class="text-center mt-3" style="margin-top: 1.5rem;">
                    <p class="text-secondary">Don't have an account? <a href="#" onclick="toggleAuthForm('register-form'); return false;">Register here</a></p>
                </div>
            </div>

            <!-- Register Form -->
            <div id="register-form" style="display: none;">
                <h3 class="mb-4">Create Account</h3>
                <form onsubmit="handleAuth(event, 'register')">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-control" pattern="[0-9]{10}" placeholder="10-digit number" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Register</button>
                </form>
                <div class="text-center mt-3" style="margin-top: 1.5rem;">
                    <p class="text-secondary">Already have an account? <a href="#" onclick="toggleAuthForm('login-form'); return false;">Login here</a></p>
                </div>
            </div>

        </div>
    </div>
</div>
