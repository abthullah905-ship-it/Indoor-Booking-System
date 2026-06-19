<?php
session_start();
$base_path = '';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - ALS Indoor</title>
    <link rel="icon" href="uploads/nav_logo1.png">
    <meta name="base-url" content="/futsalbs">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .contact-hero {
            height: 40vh;
            background: url('uploads/gallery_cricket.jpg') center/cover no-repeat;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        .contact-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.7);
        }
        .contact-hero h1 {
            position: relative;
            z-index: 1;
            color: white;
            font-size: 3rem;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
        }
        
        @media (max-width: 768px) {
            .contact-grid { grid-template-columns: 1fr; gap: 2rem; }
        }

        .contact-info-item {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .contact-info-icon {
            width: 50px;
            height: 50px;
            background: #dcfce7;
            color: var(--primary-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .map-container {
            width: 100%;
            height: 400px;
            border-radius: var(--radius-card);
            overflow: hidden;
            margin-top: 4rem;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="contact-hero">
    <h1>Contact Us</h1>
</div>

<div class="container py-5">
    <div class="contact-grid">
        
        <!-- Contact Info -->
        <div>
            <span class="section-label">Get in Touch</span>
            <h2 class="section-title mb-5">We're Here to Help</h2>
            
            <div class="contact-info-item">
                <div class="contact-info-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                </div>
                <div>
                    <h4 style="margin-bottom: 0.25rem;">Phone</h4>
                    <p class="text-secondary">+94 75 952 2543<br>+94 75 952 2542</p>
                </div>
            </div>
            
            <div class="contact-info-item">
                <div class="contact-info-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                </div>
                <div>
                    <h4 style="margin-bottom: 0.25rem;">Email</h4>
                    <p class="text-secondary">ijasahamed905@gmail.com<br>alaindoor12@gmail.com</p>
                </div>
            </div>
            
            <div class="contact-info-item">
                <div class="contact-info-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                <div>
                    <h4 style="margin-bottom: 0.25rem;">Address</h4>
                    <p class="text-secondary">Akkaraipattu 16,<br>Technical college road</p>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="card">
            <h3 class="mb-4">Send a Message</h3>
            <form id="contact-form" onsubmit="submitContact(event)">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="5" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
            </form>
        </div>
        
    </div>

    <!-- Map -->
    <div class="map-container">
        <!-- Placeholder for Google Maps iframe -->
        <!-- <p>Google Maps Embed Area</p> -->
        <iframe src="https://www.google.com/maps/embed?..." width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</div>

<?php include 'includes/auth_modal.php'; ?>
<?php include 'includes/footer.php'; ?>

<script>
function submitContact(event) {
    event.preventDefault();
    const form = event.target;
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = 'Sending...';
    btn.disabled = true;

    fetch('api/contact.php', {
        method: 'POST',
        body: new FormData(form)
    })
    .then(res => res.json())
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (data.status === 'success') {
            showToast(data.message, 'success');
            form.reset();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        showToast('Network error occurred.', 'error');
    });
}
</script>

</body>
</html>
