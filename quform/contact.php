<?php
/**
 * Contact Form Handler
 * Sends form submissions to dtglobal87@gmail.com using PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Set content type to JSON with UTF-8 charset
header('Content-Type: application/json; charset=UTF-8');

// Allow CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Initialize response array
$response = array(
    'type' => 'error',
    'error' => array(),
    'elementErrors' => array()
);

try {
    // Check if form was submitted via POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['error'][] = 'Invalid request method.';
        echo json_encode($response);
        exit;
    }

    // Sanitize and get form data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Validate required fields
    $hasErrors = false;

    if (empty($name)) {
        $response['elementErrors']['name'] = array('errors' => array('Name is required.'));
        $hasErrors = true;
    }

    if (empty($email)) {
        $response['elementErrors']['email'] = array('errors' => array('Email is required.'));
        $hasErrors = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['elementErrors']['email'] = array('errors' => array('Invalid email address.'));
        $hasErrors = true;
    }

    if (empty($subject)) {
        $response['elementErrors']['subject'] = array('errors' => array('Subject is required.'));
        $hasErrors = true;
    }

    if (empty($message)) {
        $response['elementErrors']['message'] = array('errors' => array('Message is required.'));
        $hasErrors = true;
    }

    // If there are validation errors, return them
    if ($hasErrors) {
        $response['error'][] = 'Please correct the errors below.';
        echo json_encode($response);
        exit;
    }

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    // SMTP SETTINGS
    $mail->isSMTP();
    $mail->Host       = 'localhost';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'dtglobal@kbv.6f1.mytemp.website';   // DOMAIN EMAIL
    $mail->Password   = 'd]g^e6KZGC5M';                       // EMAIL PASSWORD
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    // EMAIL SETTINGS
    $mail->setFrom('dtglobal@kbv.6f1.mytemp.website', 'DT Global Website');
    $mail->addAddress('dtglobal87@gmail.com'); // Receiver
    $mail->addReplyTo($email, $name);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Contact Form: ' . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
    
    // Build HTML email body
    $email_body = "
        <h2>New Contact Form Submission</h2>
        <p><strong>Name:</strong> " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</p>
        <p><strong>Email:</strong> " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</p>";
    
    if (!empty($phone)) {
        $email_body .= "<p><strong>Phone:</strong> " . htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') . "</p>";
    }
    
    $email_body .= "
        <p><strong>Subject:</strong> " . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . "</p>
        <hr>
        <p><strong>Message:</strong></p>
        <p>" . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "</p>
    ";
    
    $mail->Body = $email_body;
    
    // Plain text alternative
    $mail->AltBody = "New Contact Form Submission\n\n" .
                     "Name: " . $name . "\n" .
                     "Email: " . $email . "\n" .
                     (!empty($phone) ? "Phone: " . $phone . "\n" : "") .
                     "Subject: " . $subject . "\n\n" .
                     "Message:\n" . $message;

    // Send email
    $mail->send();
    
    // Success response
    $response = array(
        'type' => 'success',
        'message' => 'Thank you! Your message has been sent successfully. We will get back to you within 24 hours.'
    );

} catch (Exception $e) {
    // Error response
    $response['error'][] = 'Failed to send email: ' . $mail->ErrorInfo;
}

// Return JSON response
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>
