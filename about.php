<?php
session_start();
$base_path = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ALS Indoor</title>
    <link rel="icon" href="https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781865006/ivty3iruftjnsk3ymq1d.png">
    <meta name="base-url" content="/futsalbs">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .about-hero {
            height: 50vh;
            background: url('https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781864950/pyy7hyqslww6cxhlvzor.jpg') center/cover no-repeat;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        .about-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.75);
        }
        .about-hero h1 {
            position: relative;
            z-index: 1;
            color: white;
            font-size: 3.5rem;
        }
        
        .story-section {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            padding: 5rem 1rem;
        }
        
        .facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        .facility-item {
            text-align: center;
            padding: 2rem;
            background: var(--card-bg);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-sm);
        }
        .facility-item svg {
            width: 48px;
            height: 48px;
            color: var(--primary-green);
            margin-bottom: 1rem;
        }

        .mission-quote {
            background: var(--dark-navy);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            border-radius: var(--radius-card);
            margin: 4rem 0;
            position: relative;
            overflow: hidden;
        }
        .mission-quote h3 {
            color: white;
            font-size: 2rem;
            font-weight: 500;
            line-height: 1.5;
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="about-hero">
    <h1>About ALS Indoor</h1>
</div>

<div class="container">
    <section class="story-section">
        <span class="section-label">Our Story</span>
        <h2 class="section-title mb-4">Elevating Indoor Sports</h2>
        <p class="text-secondary" style="font-size: 1.1rem; line-height: 1.8;">
            Founded with a passion for sports, ALS Indoor was created to provide athletes of all levels a premium environment to play, train, and compete regardless of the weather. We believe that top-tier facilities shouldn't be exclusive to professionals. Our arena is designed to bring people together, foster healthy competition, and build a vibrant sporting community.
        </p>
    </section>

    <!-- Sports Cards reused from index -->
    <section class="py-5 text-center">
        <h2 class="section-title mb-5">What We Offer</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div class="card p-0" style="overflow: hidden; text-align: left;">
                <div style="height: 200px; background: url('https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781864950/pyy7hyqslww6cxhlvzor.jpg') center/cover;"></div>
                <div style="padding: 1.5rem;">
                    <h3 style="margin-bottom: 0.5rem;">Futsal Arena</h3>
                    <p class="text-secondary">FIFA certified turf offering the best grip and shock absorption for fast-paced 5v5 matches.</p>
                </div>
            </div>
            <div class="card p-0" style="overflow: hidden; text-align: left;">
                <div style="height: 200px; background: url('https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781864943/y3bxdflmbm9ci1zo11fi.jpg') center/cover;"></div>
                <div style="padding: 1.5rem;">
                    <h3 style="margin-bottom: 0.5rem;">Indoor Cricket</h3>
                    <p class="text-secondary">Fully netted arena with artificial pitch perfect for intense indoor box cricket tournaments.</p>
                </div>
            </div>
            <div class="card p-0" style="overflow: hidden; text-align: left;">
                <div style="height: 200px; background: url('https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781864936/waol1ajylcq3v2jpb9pw.jpg') center/cover;"></div>
                <div style="padding: 1.5rem;">
                    <h3 style="margin-bottom: 0.5rem;">Badminton Courts</h3>
                    <p class="text-secondary">BWF approved synthetic mats paired with anti-glare lighting for professional gameplay.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission -->
    <section class="mission-quote">
        <h3>"To empower athletes and sports enthusiasts by providing state-of-the-art facilities that inspire excellence, foster community, and promote an active lifestyle."</h3>
    </section>

    <!-- Facilities -->
    <section class="py-5">
        <div class="text-center">
            <span class="section-label">Amenities</span>
            <h2 class="section-title">Premium Facilities</h2>
        </div>
        <div class="facilities-grid">
            <div class="facility-item">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path><circle cx="7" cy="17" r="2"></circle><path d="M9 17h6"></path><circle cx="17" cy="17" r="2"></circle></svg>
                <h4>Free Parking</h4>
                <p class="text-secondary mt-2">Ample secure parking space for all players and visitors.</p>
            </div>
            <div class="facility-item">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <h4>Changing Rooms</h4>
                <p class="text-secondary mt-2">Clean, modern locker rooms with hot showers available.</p>
            </div>
            <div class="facility-item">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="M4.93 4.93l1.41 1.41"></path><path d="M17.66 17.66l1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="M6.34 17.66l-1.41 1.41"></path><path d="M19.07 4.93l-1.41 1.41"></path></svg>
                <h4>Pro Lighting</h4>
                <p class="text-secondary mt-2">Shadow-less 800 LUX LED lighting for perfect visibility.</p>
            </div>
            <div class="facility-item">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
                <h4>Cafeteria</h4>
                <p class="text-secondary mt-2">Refreshments, energy drinks, and snacks on site.</p>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/auth_modal.php'; ?>
<?php include 'includes/footer.php'; ?>

</body>
</html>
