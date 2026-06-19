<?php
session_start();
require_once 'includes/db.php';
$base_path = '';

if (!isset($_SESSION['user_id'])) {
    header("Location: booking.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];

// Get user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get bookings
$bookingsStmt = $conn->prepare("
    SELECT b.*, c.name as court_name, c.price_per_hour 
    FROM bookings b 
    JOIN courts c ON b.court_id = c.id 
    WHERE b.user_id = ? 
    ORDER BY b.booking_date DESC, b.start_time DESC
");
$bookingsStmt->bind_param("i", $user_id);
$bookingsStmt->execute();
$bookings = $bookingsStmt->get_result();

function getBadgeClass($status) {
    return match($status) {
        'Paid', 'Confirmed', 'Completed' => 'badge-success',
        'Pending' => 'badge-warning',
        'Unpaid' => 'badge-danger',
        'Cancelled' => 'badge-secondary',
        default => 'badge-primary'
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - ALS Indoor</title>
    <link rel="icon" href="uploads/nav_logo1.png">
    <meta name="base-url" content="/futsalbs">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profile-header {
            background: var(--dark-navy);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .info-card {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: var(--radius-card);
            border: 1px solid var(--border-color);
        }
        .info-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 1.125rem;
            font-weight: 500;
            color: var(--dark-navy);
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="profile-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 style="color:white; margin-bottom: 0.5rem;">My Profile</h1>
            <p style="color:rgba(255,255,255,0.8);">Manage your account and view your bookings.</p>
        </div>
        <button onclick="openModal('edit-profile-modal')" class="btn btn-primary">Edit Profile</button>
    </div>
</div>

<div class="container pb-5">
    
    <div class="card mb-5">
        <h3 class="mb-4">Account Information</h3>
        <div class="profile-info">
            <div class="info-card">
                <div class="info-label">Full Name</div>
                <div class="info-value"><?php echo htmlspecialchars($user['full_name']); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Email Address</div>
                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Phone Number</div>
                <div class="info-value"><?php echo htmlspecialchars($user['phone']); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Member Since</div>
                <div class="info-value"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Booking History</h3>
            <a href="booking.php" class="btn btn-outline btn-sm">New Booking</a>
        </div>
        
        <?php if($bookings->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Court</th>
                            <th>Date & Time</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($b = $bookings->fetch_assoc()): 
                            $timeLabel = date('g:i A', strtotime($b['start_time'])) . ' - ' . date('g:i A', strtotime('+1 hour', strtotime($b['start_time'])));
                        ?>
                        <tr id="booking-row-<?php echo $b['id']; ?>">
                            <td data-label="ID">#<?php echo $b['id']; ?></td>
                            <td data-label="Court"><?php echo htmlspecialchars($b['court_name']); ?></td>
                            <td data-label="Date & Time">
                                <?php echo htmlspecialchars($b['booking_date']); ?><br>
                                <small class="text-secondary"><?php echo $timeLabel; ?></small>
                            </td>
                            <td data-label="Amount">Rs. <?php echo number_format($b['price_per_hour']); ?></td>
                            <td data-label="Payment">
                                <span class="badge <?php echo getBadgeClass($b['payment_status']); ?>">
                                    <?php echo $b['payment_status']; ?>
                                </span>
                            </td>
                            <td data-label="Status">
                                <span class="badge <?php echo getBadgeClass($b['status']); ?>" id="status-badge-<?php echo $b['id']; ?>">
                                    <?php echo $b['status']; ?>
                                </span>
                            </td>
                            <td data-label="Action">
                                <?php if($b['status'] !== 'Cancelled' && $b['status'] !== 'Completed'): ?>
                                    <button onclick="cancelBooking(<?php echo $b['id']; ?>)" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Cancel</button>
                                <?php endif; ?>
                                
                                <?php if($b['payment_status'] === 'Unpaid' && $b['status'] !== 'Cancelled'): ?>
                                    <button onclick="openPaymentModal(<?php echo $b['id']; ?>)" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; margin-top: 5px;">Pay Now</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center p-5 text-secondary">
                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 1rem; opacity: 0.5;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <p>You haven't made any bookings yet.</p>
                <a href="booking.php" class="btn btn-primary mt-3">Book Your First Court</a>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Edit Profile Modal -->
<div class="modal-overlay" id="edit-profile-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit Profile</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form onsubmit="updateProfile(event)">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" name="phone" class="form-control" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">New Password <small class="text-secondary">(leave blank to keep current)</small></label>
                    <input type="password" name="password" class="form-control" minlength="6">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<!-- Upload Payment Modal -->
<div class="modal-overlay" id="upload-payment-modal">
    <div class="modal-content text-center" style="max-width: 400px;">
        <div class="modal-header">
            <h3 class="modal-title">Upload Payment</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p class="mb-3 text-secondary">Scan the QR code to pay using eSewa or Khalti.</p>
            <img src="uploads/qr_payment.png" alt="Payment QR" style="max-width: 200px; margin: 0 auto 1.5rem;" onerror="this.style.display='none'">
            
            <form onsubmit="uploadReceipt(event)">
                <input type="hidden" name="booking_id" id="upload_booking_id">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label class="form-label text-left">Upload Screenshot</label>
                    <input type="file" name="screenshot" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Payment</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
function updateProfile(event) {
    event.preventDefault();
    const form = event.target;
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = 'Saving...';
    btn.disabled = true;

    fetch('api/update_profile.php', {
        method: 'POST',
        body: new FormData(form)
    })
    .then(res => res.json())
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (data.status === 'success') {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
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

function cancelBooking(id) {
    if(!confirm('Are you sure you want to cancel this booking?')) return;
    
    const formData = new FormData();
    formData.append('booking_id', id);
    formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
    
    fetch('api/cancel_booking.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message, 'success');
            // Update UI inline
            const badge = document.getElementById('status-badge-' + id);
            if(badge) {
                badge.className = 'badge badge-secondary';
                badge.innerText = 'Cancelled';
            }
            // Hide cancel button
            const row = document.getElementById('booking-row-' + id);
            if(row) {
                const actionCell = row.querySelector('td[data-label="Action"]');
                if(actionCell) actionCell.innerHTML = '';
            }
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        showToast('Network error occurred.', 'error');
    });
}

function openPaymentModal(id) {
    document.getElementById('upload_booking_id').value = id;
    openModal('upload-payment-modal');
}

function uploadReceipt(event) {
    event.preventDefault();
    const form = event.target;
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = 'Uploading...';
    btn.disabled = true;

    fetch('api/upload_handler.php', {
        method: 'POST',
        body: new FormData(form)
    })
    .then(res => res.json())
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (data.status === 'success') {
            showToast('Receipt uploaded successfully!', 'success');
            closeModal('upload-payment-modal');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        showToast('Upload failed due to network error.', 'error');
    });
}
</script>

</body>
</html>
