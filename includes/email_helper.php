<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Helper to get a configured PHPMailer instance.
 */
function getMailer(): PHPMailer {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->SMTPDebug  = 0;
    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->isHTML(true);
    return $mail;
}

/**
 * 1. Send Booking Confirmation (To User)
 */
function sendBookingConfirmation(string $toEmail, string $toName, array $booking): bool {
    try {
        $mail = getMailer();
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = '✅ Booking Confirmed – ALS Indoor #' . $booking['id'];

        $date      = htmlspecialchars($booking['booking_date']);
        $time      = htmlspecialchars($booking['time_label'] ?? $booking['start_time']);
        $court     = htmlspecialchars($booking['court_name'] ?? 'Court');
        $price     = number_format($booking['price_per_hour'] ?? $booking['amount'] ?? 0);
        $bookingId = (int)$booking['id'];

        $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;border:1px solid #eee;border-radius:10px;overflow:hidden;'>
          <div style='background:#5cb85c;padding:20px;text-align:center;'>
            <h1 style='color:#fff;margin:0;'>ALS Indoor</h1>
          </div>
          <div style='padding:30px;'>
            <h2 style='color:#333;'>Booking Confirmed! 🎉</h2>
            <p style='color:#555;'>Hi <strong>{$toName}</strong>, your court has been successfully booked.</p>
            <table style='width:100%;border-collapse:collapse;margin-top:20px;'>
              <tr style='background:#f5f7fa;'><td style='padding:12px;font-weight:bold;color:#555;'>Booking ID</td><td style='padding:12px;'>#{$bookingId}</td></tr>
              <tr><td style='padding:12px;font-weight:bold;color:#555;'>Court</td><td style='padding:12px;'>{$court}</td></tr>
              <tr style='background:#f5f7fa;'><td style='padding:12px;font-weight:bold;color:#555;'>Date</td><td style='padding:12px;'>{$date}</td></tr>
              <tr><td style='padding:12px;font-weight:bold;color:#555;'>Time</td><td style='padding:12px;'>{$time}</td></tr>
              <tr style='background:#f5f7fa;'><td style='padding:12px;font-weight:bold;color:#555;'>Amount</td><td style='padding:12px;'>Rs. {$price}</td></tr>
            </table>
            <p style='margin-top:25px;color:#777;font-size:0.9rem;'>Please complete your payment via the QR code provided on site or upload a payment screenshot in your profile. If you have any questions, contact us.</p>
          </div>
          <div style='background:#0f172a;padding:15px;text-align:center;color:#aaa;font-size:0.85rem;'>&copy; ALS Indoor Sports Arena</div>
        </div>";

        $mail->AltBody = "Booking Confirmed!\nBooking ID: #{$bookingId}\nCourt: {$court}\nDate: {$date}\nTime: {$time}\nAmount: Rs. {$price}\n\nThank you for booking with ALS Indoor!";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('sendBookingConfirmation Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 2. Send Booking Cancellation (To User/Admin)
 */
function sendBookingCancellation(string $toEmail, string $toName, array $booking): bool {
    try {
        $mail = getMailer();
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = '❌ Booking Cancelled – ALS Indoor #' . $booking['id'];

        $date      = htmlspecialchars($booking['booking_date']);
        $time      = htmlspecialchars($booking['time_label'] ?? $booking['start_time']);
        $court     = htmlspecialchars($booking['court_name'] ?? 'Court');
        $bookingId = (int)$booking['id'];

        $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;border:1px solid #eee;border-radius:10px;overflow:hidden;'>
          <div style='background:#ef4444;padding:20px;text-align:center;'>
            <h1 style='color:#fff;margin:0;'>ALS Indoor</h1>
          </div>
          <div style='padding:30px;'>
            <h2 style='color:#333;'>Booking Cancelled</h2>
            <p style='color:#555;'>Hi <strong>{$toName}</strong>, your booking has been cancelled.</p>
            <table style='width:100%;border-collapse:collapse;margin-top:20px;'>
              <tr style='background:#f5f7fa;'><td style='padding:12px;font-weight:bold;color:#555;'>Booking ID</td><td style='padding:12px;'>#{$bookingId}</td></tr>
              <tr><td style='padding:12px;font-weight:bold;color:#555;'>Court</td><td style='padding:12px;'>{$court}</td></tr>
              <tr style='background:#f5f7fa;'><td style='padding:12px;font-weight:bold;color:#555;'>Date</td><td style='padding:12px;'>{$date}</td></tr>
              <tr><td style='padding:12px;font-weight:bold;color:#555;'>Time</td><td style='padding:12px;'>{$time}</td></tr>
            </table>
            <p style='margin-top:25px;color:#777;font-size:0.9rem;'>The slot is now available again.</p>
          </div>
          <div style='background:#0f172a;padding:15px;text-align:center;color:#aaa;font-size:0.85rem;'>&copy; ALS Indoor Sports Arena</div>
        </div>";

        $mail->AltBody = "Booking Cancelled\nBooking ID: #{$bookingId}\nCourt: {$court}\nDate: {$date}\nTime: {$time}\n\nYour booking has been cancelled.";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('sendBookingCancellation Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 3. Send Payment Verified (To User)
 */
function sendPaymentVerified(string $toEmail, string $toName, array $booking): bool {
    try {
        $mail = getMailer();
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = '💰 Payment Confirmed – ALS Indoor #' . $booking['id'];

        $bookingId = (int)$booking['id'];
        $court     = htmlspecialchars($booking['court_name'] ?? 'Court');

        $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;border:1px solid #eee;border-radius:10px;overflow:hidden;'>
          <div style='background:#22c55e;padding:20px;text-align:center;'>
            <h1 style='color:#fff;margin:0;'>ALS Indoor</h1>
          </div>
          <div style='padding:30px;'>
            <h2 style='color:#333;'>Payment Verified</h2>
            <p style='color:#555;'>Hi <strong>{$toName}</strong>, your payment for Booking #{$bookingId} ({$court}) has been verified.</p>
            <p style='color:#555;'>Your booking status is now <strong>Confirmed</strong>. See you at the court!</p>
          </div>
          <div style='background:#0f172a;padding:15px;text-align:center;color:#aaa;font-size:0.85rem;'>&copy; ALS Indoor Sports Arena</div>
        </div>";

        $mail->AltBody = "Payment Verified!\nYour payment for Booking #{$bookingId} has been confirmed.";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('sendPaymentVerified Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 4. Send Booking Completed (To User)
 */
function sendBookingCompleted(string $toEmail, string $toName, array $booking): bool {
    try {
        $mail = getMailer();
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = '⚽ Hope you had a great game! – ALS Indoor #' . $booking['id'];

        $bookingId = (int)$booking['id'];

        $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;border:1px solid #eee;border-radius:10px;overflow:hidden;'>
          <div style='background:#0f172a;padding:20px;text-align:center;'>
            <h1 style='color:#fff;margin:0;'>ALS Indoor</h1>
          </div>
          <div style='padding:30px;'>
            <h2 style='color:#333;'>Thank you for playing!</h2>
            <p style='color:#555;'>Hi <strong>{$toName}</strong>, your booking #{$bookingId} is now complete.</p>
            <p style='color:#555;'>We hope you enjoyed your time at ALS Indoor. We look forward to hosting your next game!</p>
          </div>
          <div style='background:#0f172a;padding:15px;text-align:center;color:#aaa;font-size:0.85rem;'>&copy; ALS Indoor Sports Arena</div>
        </div>";

        $mail->AltBody = "Thank you for playing!\nYour booking #{$bookingId} is complete. We hope you enjoyed your time!";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('sendBookingCompleted Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 5. Send Admin New Booking Notification
 */
function sendAdminNewBooking(string $adminEmail, array $booking, array $user): bool {
    try {
        $mail = getMailer();
        $mail->addAddress($adminEmail, 'Admin');
        $mail->Subject = '🔔 New Booking Alert: ' . $booking['court_name'];

        $date      = htmlspecialchars($booking['booking_date']);
        $time      = htmlspecialchars($booking['time_label'] ?? $booking['start_time']);
        $court     = htmlspecialchars($booking['court_name'] ?? 'Court');
        $userName  = htmlspecialchars($user['full_name']);
        $userPhone = htmlspecialchars($user['phone']);

        $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;'>
            <h3>New Booking Alert</h3>
            <p><strong>Customer:</strong> {$userName} ({$userPhone})</p>
            <p><strong>Court:</strong> {$court}</p>
            <p><strong>Date & Time:</strong> {$date} at {$time}</p>
            <p>Please check the admin dashboard for details.</p>
        </div>";

        $mail->AltBody = "New Booking Alert\nCustomer: {$userName} ({$userPhone})\nCourt: {$court}\nDate: {$date} at {$time}";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('sendAdminNewBooking Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 6. Send Admin Contact Message Notification
 */
function sendAdminContactMessage(string $adminEmail, array $messageData): bool {
    try {
        $mail = getMailer();
        $mail->addAddress($adminEmail, 'Admin');
        $mail->Subject = '📩 New Contact Form Message: ' . $messageData['subject'];

        $name    = htmlspecialchars($messageData['name']);
        $email   = htmlspecialchars($messageData['email']);
        $subject = htmlspecialchars($messageData['subject']);
        $message = nl2br(htmlspecialchars($messageData['message']));

        $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;'>
            <h3>New Contact Message</h3>
            <p><strong>From:</strong> {$name} ({$email})</p>
            <p><strong>Subject:</strong> {$subject}</p>
            <hr>
            <p>{$message}</p>
        </div>";

        $mail->AltBody = "New Contact Message\nFrom: {$name} ({$email})\nSubject: {$subject}\n\n{$messageData['message']}";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('sendAdminContactMessage Error: ' . $e->getMessage());
        return false;
    }
}
?>
