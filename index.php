<?php
session_start();
require_once 'includes/db.php';
$base_path = '';

// Fetch stats
$stats = [
    'bookings' => 0,
    'players' => 0,
    'courts' => 0,
    'hours' => 0
];

$res = $conn->query("SELECT COUNT(*) as c FROM bookings");
if ($row = $res->fetch_assoc()) $stats['bookings'] = $row['c'];

$res = $conn->query("SELECT COUNT(*) as c FROM users");
if ($row = $res->fetch_assoc()) $stats['players'] = $row['c'];

$res = $conn->query("SELECT COUNT(*) as c FROM courts WHERE is_active=1");
if ($row = $res->fetch_assoc()) $stats['courts'] = $row['c'];

// Estimate hours (each booking is 1 hr)
$stats['hours'] = $stats['bookings'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - ALS Indoor</title>
    <link rel="icon" href="https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781865006/ivty3iruftjnsk3ymq1d.png">
    <meta name="base-url" content="/futsalbs">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Hero Section */
        .hero {
            height: 100vh;
            background: url('https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781865001/oeqoy1lvnmbqhwkk3y2q.png') center/cover no-repeat;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 0 1rem;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.7);
        }
        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
        }
        .hero h1 {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            color: white;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            color: rgba(255,255,255,0.9);
        }
        
        /* Features */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }
        .feature-card {
            background: var(--card-bg);
            padding: 2rem;
            text-align: center;
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-md);
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            background: #dcfce7;
            color: var(--primary-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        /* Stats Section */
        .stats-section {
            background: var(--dark-navy);
            color: white;
            padding: 4rem 0;
            text-align: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        .stat-item h3 {
            font-size: 3rem;
            color: var(--primary-green);
            margin-bottom: 0.5rem;
        }

        /* Sports Cards */
        .sports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        .sport-card {
            border-radius: var(--radius-card);
            overflow: hidden;
            background: var(--card-bg);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }
        .sport-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-5px);
        }
        .sport-img {
            height: 200px;
            background-size: cover;
            background-position: center;
        }

        /* Gallery */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }
        .gallery-item {
            height: 200px;
            border-radius: var(--radius-card);
            background-size: cover;
            background-position: center;
            transition: var(--transition);
        }
        .gallery-item:hover {
            transform: scale(1.02);
        }

        /* CTA Banner */
        .cta-banner {
            background: var(--primary-green);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            border-radius: var(--radius-card);
        }
        .cta-banner h2 { color: white; margin-bottom: 1.5rem; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- Hero -->
<section class="hero">
    <div class="hero-content">
        <h1>Your Game, Your Court, Your Time</h1>
        <p>Book your court, challenge your friends, and enjoy the ultimate indoor sports experience at ALS Indoor.</p>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="booking.php" class="btn btn-primary" style="font-size: 1.25rem; padding: 1rem 2rem;">Book a Court</a>
        <?php else: ?>
            <a href="#" onclick="openModal('auth-modal'); return false;" class="btn btn-primary" style="font-size: 1.25rem; padding: 1rem 2rem;">Book a Court Now</a>
        <?php endif; ?>
    </div>
</section>

<div class="container">
    <!-- Features -->
    <div class="features-grid my-5">
        <div class="feature-card">
            <div class="feature-icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></div>
            <h4>Easy Booking</h4>
            <p class="text-secondary mt-2">Book your court in seconds with our simple interface.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></div>
            <h4>Live Availability</h4>
            <p class="text-secondary mt-2">See real-time slot availability before you book.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></div>
            <h4>Secure Payment</h4>
            <p class="text-secondary mt-2">Multiple safe payment options including digital wallets.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg></div>
            <h4>Mobile Friendly</h4>
            <p class="text-secondary mt-2">Book easily from any device, anywhere, anytime.</p>
        </div>
    </div>
</div>

<!-- Stats Section -->
<section class="stats-section my-5">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <h3><?php echo $stats['bookings']; ?>+</h3>
                <p>Total Bookings</p>
            </div>
            <div class="stat-item">
                <h3><?php echo $stats['players']; ?>+</h3>
                <p>Happy Players</p>
            </div>
            <div class="stat-item">
                <h3><?php echo $stats['courts']; ?></h3>
                <p>Available Courts</p>
            </div>
            <div class="stat-item">
                <h3><?php echo $stats['hours']; ?>+</h3>
                <p>Hours Played</p>
            </div>
        </div>
    </div>
</section>

<!-- Sports -->
<section class="container py-5 text-center">
    <span class="section-label">Our Facilities</span>
    <h2 class="section-title mb-5">Choose Your Sport</h2>
    
    <div class="sports-grid" style="margin-top: 3rem;">
        <div class="sport-card text-left">
            <div class="sport-img" style="background-image: url('https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781864950/pyy7hyqslww6cxhlvzor.jpg');"></div>
            <div class="p-4" style="padding: 1.5rem; text-align: left;">
                <h3 style="margin-bottom: 0.5rem;">Futsal</h3>
                <p class="text-secondary mb-4" style="margin-bottom: 1.5rem;">FIFA standard artificial turf with shock pads. Perfect for 5v5 or 7v7 matches.</p>
                <a href="booking.php" class="btn btn-outline" style="width: 100%;">Book This Sport</a>
            </div>
        </div>
        <div class="sport-card text-left">
            <div class="sport-img" style="background-image: url('https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781864943/y3bxdflmbm9ci1zo11fi.jpg');"></div>
            <div class="p-4" style="padding: 1.5rem; text-align: left;">
                <h3 style="margin-bottom: 0.5rem;">Indoor</h3>
                <p class="text-secondary mb-4" style="margin-bottom: 1.5rem;">Tension-net enclosed arena with professional synthetic pitch.</p>
                <a href="booking.php" class="btn btn-outline" style="width: 100%;">Book This Sport</a>
            </div>
        </div>
        <div class="sport-card text-left">
            <div class="sport-img" style="background-image: url('https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781864936/waol1ajylcq3v2jpb9pw.jpg');"></div>
            <div class="p-4" style="padding: 1.5rem; text-align: left;">
                <h3 style="margin-bottom: 0.5rem;">Padel</h3>
                <p class="text-secondary mb-4" style="margin-bottom: 1.5rem;">BWF approved synthetic mats with anti-glare professional lighting.</p>
                <a href="booking.php" class="btn btn-outline" style="width: 100%;">Book This Sport</a>
            </div>
        </div>
    </div>
</section>

<!-- Gallery -->
<section class="container py-5 text-center">
    <span class="section-label">Gallery</span>
    <h2 class="section-title mb-5">Inside ALS Indoor</h2>
    
    <div class="gallery-grid" style="margin-top: 3rem;">
        <div class="gallery-item" style="background-image: url('https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781864988/okv7rrjtzvlomg5cvwox.jpg');"></div>
        <div class="gallery-item" style="background-image: url('https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781865001/oeqoy1lvnmbqhwkk3y2q.png');"></div>
        <div class="gallery-item" style="background-image: url('https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781864918/b88zeuzsonjxo4owhtna.jpg');"></div>
        <div class="gallery-item" style="background-image: url('https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781864950/pyy7hyqslww6cxhlvzor.jpg');"></div>
    </div>
</section>

<!-- CTA -->
<section class="container py-5">
    <div class="cta-banner">
        <h2>Ready to Play?</h2>
        <p style="margin-bottom: 2rem; font-size: 1.1rem; color: rgba(255,255,255,0.9);">Join hundreds of players who have already experienced our premium courts.</p>
        <a href="booking.php" class="btn btn-outline" style="border-color: white; color: white; padding: 0.75rem 2rem; font-size: 1.1rem; background: transparent;">Book Now</a>
    </div>
</section>

<?php include 'includes/auth_modal.php'; ?>
<?php include 'includes/footer.php'; ?>

<script>
    // Stats animation
    const stats = document.querySelectorAll('.stat-item h3');
    stats.forEach(stat => {
        const text = stat.innerText;
        const target = parseInt(text.replace(/\D/g, ''));
        if(isNaN(target) || target === 0) return;
        
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                stat.innerText = target + (text.includes('+') ? '+' : '');
                clearInterval(timer);
            } else {
                stat.innerText = Math.ceil(current) + (text.includes('+') ? '+' : '');
            }
        }, 30);
    });
</script>
</body>
</html>
