<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isAdmin()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $to = sanitize($input['to'] ?? '');
    $subject = sanitize($input['subject'] ?? '');
    $body = $input['body'] ?? '';

    if (empty($to) || empty($subject) || empty($body)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    // Try PHPMailer first, fall back to mail()
    $phpmailerPath = __DIR__ . '/../vendor/phpmailer/PHPMailer.php';

    if (file_exists($phpmailerPath)) {
        require_once __DIR__ . '/../vendor/phpmailer/PHPMailer.php';
        require_once __DIR__ . '/../vendor/phpmailer/SMTP.php';
        require_once __DIR__ . '/../vendor/phpmailer/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            echo json_encode(['success' => true, 'message' => 'Email sent!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'PHPMailer error: ' . $e->getMessage()]);
        }
    } else {
        // Fallback to PHP mail()
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";

        if (mail($to, $subject, $body, $headers)) {
            echo json_encode(['success' => true, 'message' => 'Email sent!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send email. Please check server configuration.']);
        }
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
