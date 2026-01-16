<?php
// Set JSON response header for quform
header('Content-Type: application/json; charset=UTF-8');

// Initialize response array (quform format)
$response = array(
    'type' => 'error',
    'error' => array(),
    'elementErrors' => array()
);

// Get form data
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation
$hasErrors = false;

if ($name === '') {
    $response['elementErrors']['name'] = array('errors' => array('Name is required.'));
    $hasErrors = true;
}

if ($email === '') {
    $response['elementErrors']['email'] = array('errors' => array('Email is required.'));
    $hasErrors = true;
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['elementErrors']['email'] = array('errors' => array('Invalid email address.'));
    $hasErrors = true;
}

if ($subject === '') {
    $response['elementErrors']['subject'] = array('errors' => array('Subject is required.'));
    $hasErrors = true;
}

if ($message === '') {
    $response['elementErrors']['message'] = array('errors' => array('Message is required.'));
    $hasErrors = true;
}

// If validation errors, return them
if ($hasErrors) {
    $response['error'][] = 'Please correct the errors below.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Build email message
$email_message = "New Contact Form Submission\n\n";
$email_message .= "Name: " . $name . "\n";
$email_message .= "Email: " . $email . "\n";
if (!empty($phone)) {
    $email_message .= "Phone: " . $phone . "\n";
}
$email_message .= "Subject: " . $subject . "\n\n";
$email_message .= "Message:\n" . $message;

// In case any of our lines are larger than 70 characters, we should use wordwrap()
$email_message = wordwrap($email_message, 70, "\r\n");

// Send email
$mail_sent = @mail('dtglobal87@gmail.com', 'Contact Form: ' . $subject, $email_message);

// Return response
if ($mail_sent) {
    // Success response
    $response = array(
        'type' => 'success',
        'message' => 'Thank you! Your message has been sent successfully. We will get back to you within 24 hours.'
    );
} else {
    // Error response
    $response['error'][] = 'Failed to send email. Please try again later.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>