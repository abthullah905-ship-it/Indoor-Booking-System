<?php
session_start();
require_once 'includes/db.php';
$base_path = '';

// Generate CSRF token if missing
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch active courts
$courts = [];
$res = $conn->query("SELECT id, name, price_per_hour, sport_type FROM courts WHERE is_active=1 ORDER BY name ASC");
while($row = $res->fetch_assoc()) {
    $courts[] = $row;
}

$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Court - ALS Indoor</title>
    <link rel="icon" href="uploads/nav_logo1.png">
    <meta name="base-url" content="/futsalbs">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .booking-hero {
            height: 40vh;
            background: url('uploads/gallery_football.jpg') center/cover no-repeat;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        .booking-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.7);
        }
        .booking-hero h1 {
            position: relative;
            z-index: 1;
            color: white;
            font-size: 3rem;
        }

        .booking-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2rem;
            align-items: start;
        }
        
        @media (max-width: 900px) {
            .booking-container { grid-template-columns: 1fr; }
        }

        .slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .slot {
            padding: 1rem;
            border-radius: var(--radius-btn);
            text-align: center;
            cursor: pointer;
            border: 2px solid transparent;
            transition: var(--transition);
            font-weight: 500;
        }
        .slot.available {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0;
        }
        .slot.available:hover {
            background: #bbf7d0;
            transform: translateY(-2px);
        }
        .slot.selected {
            background: var(--primary-green);
            color: white;
            border-color: var(--primary-green-hover);
        }
        .slot.booked {
            background: #fee2e2;
            color: #991b1b;
            cursor: not-allowed;
            border-color: #fecaca;
            opacity: 0.8;
        }
        .slot.past {
            background: #f1f5f9;
            color: #64748b;
            cursor: not-allowed;
            border-color: #e2e8f0;
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="booking-hero">
    <h1>Book Your Court</h1>
</div>

<div class="container py-5">
    <div class="booking-container">
        
        <!-- Availability Grid -->
        <div class="card">
            <h3 class="mb-4">Select a Time Slot</h3>
            
            <div id="loading-slots" style="display: none; text-align: center; padding: 2rem;">
                Loading slots...
            </div>
            
            <div id="slots-container" class="slots-grid">
                <!-- Slots populated by JS -->
            </div>
        </div>

        <!-- Booking Form -->
        <div class="card">
            <h3 class="mb-4">Booking Details</h3>
            <form id="booking-form" onsubmit="submitBooking(event)">
                <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="start_time" id="start_time" value="" required>
                
                <div class="form-group">
                    <label class="form-label">Select Court</label>
                    <select name="court_id" id="court_id" class="form-control" onchange="loadAvailability()" required>
                        <option value="">-- Choose a Court --</option>
                        <?php foreach($courts as $c): ?>
                            <option value="<?php echo $c['id']; ?>" data-price="<?php echo $c['price_per_hour']; ?>">
                                <?php echo htmlspecialchars($c['name']) . ' (' . ucfirst($c['sport_type']) . ') - Rs. ' . number_format($c['price_per_hour']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Select Date</label>
                    <input type="date" name="booking_date" id="booking_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>" onchange="loadAvailability()" required>
                </div>
                
                <div class="form-group mt-4" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Selected Time:</span>
                        <strong id="display_time">None</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-secondary">Amount:</span>
                        <strong id="display_price" class="text-primary" style="font-size: 1.25rem;">Rs. 0</strong>
                    </div>
                    
                    <?php if($is_logged_in): ?>
                        <button type="submit" class="btn btn-primary" style="width:100%; font-size:1.1rem; padding:0.75rem;">Confirm Booking</button>
                    <?php else: ?>
                        <button type="button" onclick="openModal('auth-modal')" class="btn btn-primary" style="width:100%; font-size:1.1rem; padding:0.75rem;">Login to Book</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal-overlay" id="payment-modal">
    <div class="modal-content text-center" style="max-width: 450px;">
        <div class="modal-header">
            <h3 class="modal-title">Complete Payment</h3>
            <button class="modal-close" onclick="closePaymentModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="p-3 mb-4" style="background:#f8fafc; border-radius: var(--radius-card);">
                <h4 class="text-primary mb-2">Booking ID: #<span id="pay_booking_id"></span></h4>
                <p class="mb-1" id="pay_court_name"></p>
                <p class="mb-1" id="pay_date_time"></p>
                <h3 class="mt-3">Total: <span id="pay_amount"></span></h3>
            </div>
            
            <h2>Bank Details</h2>

            <div class="p-3 mb-4" style="background:#f8fafc; border-radius: var(--radius-card);">
                <h4 class="text-primary mb-2">Account info<span id="pay_booking_id"></span></h4>
                <p>Commercial Bank<br>Akkaraipattu</p>
                <h3 class="mt-3">4199 0710 1775 9584</h3>
            </div>
            
            <form id="upload-form" onsubmit="uploadReceipt(event)">
                <input type="hidden" name="booking_id" id="upload_booking_id">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label class="form-label text-left">Upload Screenshot</label>
                    <input type="file" name="screenshot" id="screenshot" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Payment</button>
            </form>
            
            <button onclick="payLater()" class="btn btn-outline mt-3" style="width: 100%;">Pay Later</button>
        </div>
    </div>
</div>

<?php include 'includes/auth_modal.php'; ?>
<?php include 'includes/footer.php'; ?>

<script src="assets/js/booking.js"></script>
<script>
    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        loadAvailability();
    });
</script>
</body>
</html>
