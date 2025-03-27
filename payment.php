<?php
session_start();
if (!isset($_GET['order_id']) || !isset($_GET['amount'])) {
    die("❌ Invalid payment request.");
}

// ✅ Order details
$order_id = $_GET['order_id'];
$amount = $_GET['amount'];
$upi_id = "9470446751@ptyes"; 
$payee_name = "Tuckin"; 

// ✅ UPI Payment URL
$upi_link = "upi://pay?pa={$upi_id}&pn={$payee_name}&mc=&tid={$order_id}&tr={$order_id}&tn=Food Order Payment&am={$amount}&cu=INR";

// ✅ QR Code API URL
$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($upi_link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPI Payment</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; img align:centre; padding: 20px; }
        .container { max-width: 400px; margin: auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
        img { width: 100%; max-width: 250px; margin: 20px 0; display: block; }
        .btn { display: none; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px; }
        .btn:hover { background-color: #0056b3; }
        #countdown { font-size: 20px; font-weight: bold; color: red; }
        #qr-container {
    text-align: center; /* Center the QR code */
}

#qr-container img {
    display: block; /* Ensure proper centering */
    margin: 0 auto; /* Center the image */
}

    </style>
    <script>
        let countdown = 5;
        let countdownTimer = setInterval(function() {
            document.getElementById("countdown").innerText = countdown;
            countdown--;

            if (countdown < 0) {
                clearInterval(countdownTimer);
                document.getElementById("qr-container").style.display = "none"; // Hide QR code
                document.getElementById("countdown-container").style.display = "none"; // Hide countdown
                document.getElementById("paid-btn").style.display = "inline-block"; // Show button
            }
        }, 1000);
    </script>
</head>
<body>

<div class="container">
    <h2>Scan & Pay via UPI</h2>
    <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
    <p><strong>Total Amount:</strong> ₹<?php echo $amount; ?></p>

    <!-- ✅ Countdown Timer -->
    <div id="countdown-container">
        <p>QR will disappear in <span id="countdown">5</span> seconds...</p>
    </div>

    <!-- ✅ QR Code -->
    <div id="qr-container">
        <img src="<?php echo $qr_code_url; ?>" alt="UPI QR Code">
    </div>

    <!-- ✅ "I Have Paid" Button (Initially hidden, appears after countdown) -->
    <form action="verify_payment.php" method="POST">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
        <input type="hidden" name="amount" value="<?php echo $amount; ?>">
        <button type="submit" id="paid-btn" class="btn">I Have Paid</button>
    </form>
</div>

</body>
</html>
