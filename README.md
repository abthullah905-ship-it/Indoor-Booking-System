# ALS Indoor - Futsal Booking System

ALS Indoor is a comprehensive web-based sports venue booking system built with PHP and MySQL. It allows users to view real-time availability, book courts, and manage their profiles, while providing administrators with a robust dashboard to manage bookings, users, and courts.

## Features

- **User Authentication:** Registration, login, and secure password reset functionalities.
- **Real-Time Booking:** Check court availability in real-time and book slots for Futsal, Indoor Cricket, and Padel.
- **User Dashboard:** Manage personal profile and view booking history.
- **Admin Panel:** Complete administrative control over users, courts, and bookings.
- **Secure Payments:** Integration with various secure payment methods.
- **Responsive Design:** Mobile-friendly interface for booking on the go.

## Tech Stack

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Email Service:** PHPMailer (via Composer)

## Project Structure

- `admin/` - Administrative dashboard and backend logic.
- `api/` - API endpoints for asynchronous requests.
- `assets/` - CSS, JavaScript, and other static assets.
- `includes/` - Reusable PHP components (navbar, footer, DB connection).
- `uploads/` - Uploaded images and media.
- `db.sql` - Database schema and initial data setup.
- `composer.json` - PHP dependencies configuration.

## Installation and Setup

1. **Prerequisites:** 
   - A local server environment like XAMPP, WAMP, or MAMP.
   - Composer installed for PHP dependencies.

2. **Clone/Copy the Project:**
   Place the project folder (`futsalbs`) into your web server's root directory (e.g., `c:\xampp\htdocs\futsalbs`).

3. **Database Setup:**
   - Open phpMyAdmin or your preferred MySQL client.
   - Create a new database.
   - Import the provided `db.sql` file into the newly created database.

4. **Configuration:**
   - Update the database connection settings in `includes/db.php` if your database credentials differ from the defaults (typically username: `root`, password: empty).

5. **Install Dependencies:**
   - Open a terminal or command prompt in the project root directory.
   - Run `composer install` to install PHPMailer and other required dependencies.

6. **Access the Application:**
   - Open your web browser and navigate to `http://localhost/futsalbs`.

## Usage

- **Guests:** Can browse the available sports, view features, and check the gallery.
- **Registered Users:** Can log in to book a court, view their booking history, and manage their profile.
- **Administrators:** Can access the `/admin` path to manage the entire platform.
