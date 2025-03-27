<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Change this for other email providers
    $mail->SMTPAuth = true;
    $mail->Username = 'innocentone648@gmail.com'; // Your email
    $mail->Password = 'oquo gchh huqz pquv'; // Your email password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('innocentone648@gmail.com', 'Admin');
    $mail->addAddress('onlyforone41@gmail.com', 'user'); // Change recipient email

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Order Status Update';
    $mail->Body    = 'Your food order is ready! 🍕';

    $mail->send();
    echo '✅ Email has been sent successfully!';
} catch (Exception $e) {
    echo "❌ Email could not be sent. Error: {$mail->ErrorInfo}";
}
?>
