<?php
session_start();
require 'vendor/autoload.php'; // Load PHPMailer and MongoDB

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use MongoDB\BSON\ObjectId;

// MongoDB connection
include 'db_connection.php';

//$client = new MongoDB\Client("mongodb://localhost:27017");
$database = $client->food_ordering; // Replace with your MongoDB database name
$ordersCollection = $database->orders; // Collection for orders
$usersCollection = $database->users; // Collection for users

// ✅ Check if order ID and amount are received
if (!isset($_POST['order_id']) || !isset($_POST['amount'])) {
    die("❌ Invalid request.");
}

$order_id = $_POST['order_id'];
$amount = $_POST['amount'];

try {
    $order = $ordersCollection->findOne(['_id' => new ObjectId($order_id)]);
} catch (Exception $e) {
    die("❌ Invalid Order ID format.");
}

if (!$order) {
    die("❌ Order not found.");
}

// ✅ Fetch user email from MongoDB
$user_id = $order['user_id'];

try {
    $user = $usersCollection->findOne(['_id' => new ObjectId($user_id)]);
} catch (Exception $e) {
    die("❌ Invalid User ID format.");
}

if (!$user) {
    die("❌ User not found.");
}

$user_email = $user['email'];

// ✅ Update order status to "Paid"
$updateResult = $ordersCollection->updateOne(
    ['_id' => new ObjectId($order_id)],
    ['$set' => ['status' => 'Paid']]
);

if ($updateResult->getModifiedCount() == 0) {
    die("❌ Failed to update order status.");
}

// ✅ Send Payment Confirmation Email
$mail = new PHPMailer(true);
try {
    // ✅ SMTP settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'innocentone648@gmail.com'; // Admin email
    $mail->Password = 'oquo gchh huqz pquv'; // Gmail App Password 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // ✅ Email details
    $mail->setFrom('innocentone648@gmail.com', 'Tuckin Food Order');
    $mail->addAddress($user_email);

    // ✅ Email content
    $mail->isHTML(true);
    $mail->Subject = "Payment Confirmation - Order #{$order_id}";
    $mail->Body = "
        <h3>🎉 Payment Successful!</h3>
        <p>Your order ID: <strong>#{$order_id}</strong></p>
        <p>Total Amount Paid: <strong>₹{$amount}</strong></p>
        <p>Thank you for your order. Your food is being prepared! 🍕</p>
    ";

    $mail->send();
    echo "✅ Payment confirmed! A confirmation email has been sent.<a href='index.php'>Return to Home Page</a>";
   // header("Location: index.php");
    exit;
} catch (Exception $e) {
    echo "✅ Payment confirmed, but email could not be sent. Error: {$mail->ErrorInfo}";
}
?>
