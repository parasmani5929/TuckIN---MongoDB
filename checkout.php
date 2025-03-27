<?php 
session_start();
include 'db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("❌ You must be logged in to place an order. <a href='login.html'>Login here</a>");
}

// ✅ Ensure cart is not empty
if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
    die("🛒 Your cart is empty. <a href='index.php'>Go back to menu</a>");
}

// ✅ Fetch user's email from MongoDB
$user = $db->users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);

if (!$user) {
    die("❌ Error: User not found.");
}

$user_email = $user['email']; // ✅ User's email

// ✅ Calculate Total Price
$total_price = 0;
foreach ($_SESSION["cart"] as $food_id => $quantity) {
    $food = $db->food_items->findOne(['_id' => new MongoDB\BSON\ObjectId($food_id)]);
    if ($food) {
        $total_price += $food["price"] * $quantity;
    }
}
?>

<!-- ✅ Checkout Form -->
<form action="checkout.php" method="POST">
    <h3>Select Payment Method:</h3>
    
    <div class="payment-options">
        <!-- ✅ UPI Payment Button -->
        <button type="submit" name="payment_method" value="upi" class="payment-btn upi-btn">
            Pay Now (UPI QR Code)
        </button>

        <!-- ✅ Cash on Delivery Button -->
        <button type="submit" name="payment_method" value="cod" class="payment-btn cod-btn">
            Cash on Delivery
        </button>
    </div>

    <!-- ✅ User Review Input -->
    <div class="review-section">
        <h3>Leave a Review (Optional):</h3>
        <textarea name="user_review" id="user_review" placeholder="Write your review here..." rows="4"></textarea>
        <p id="review_error" style="color: red; display: none;">⚠️ Please write a review before placing your order.</p>
    </div>

    <!-- ✅ JavaScript to Validate Review -->
<script>
    function validateForm() {
        var review = document.getElementById("user_review").value.trim();
        var errorText = document.getElementById("review_error");

        if (review === "") {
            errorText.style.display = "block"; // Show error message
            return false; // Prevent form submission
        } else {
            errorText.style.display = "none"; // Hide error message
            return true; // Allow form submission
        }
    }
</script>

</form>

<!-- ✅ Styles for Better UI -->
<style>
    body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
    h3 { margin-bottom: 15px; font-size: 20px; }
    .payment-options { display: flex; justify-content: center; gap: 20px; margin-bottom: 20px; }
    .payment-btn {
        border: none;
        padding: 15px 20px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 8px;
        transition: 0.3s;
        width: 180px;
    }
    .upi-btn { background-color: #007bff; color: white; }
    .upi-btn:hover { background-color: #0056b3; }
    .cod-btn { background-color: #28a745; color: white; }
    .cod-btn:hover { background-color: #1e7e34; }
    .review-section { margin-top: 20px; }
    textarea {
        width: 100%;
        max-width: 400px;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .submit-btn {
        background-color: #ff9800;
        color: white;
        border: none;
        padding: 12px 20px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 8px;
        margin-top: 15px;
        transition: 0.3s;
    }
    .submit-btn:hover { background-color: #e68900; }
</style>

<?php
// ✅ Check if payment method is selected
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['payment_method'])) {
        die("❌ Please select a payment method. <a href='checkout.php'>Go back</a>");
    }

    $payment_method = $_POST['payment_method'];
    $user_review = isset($_POST['user_review']) ? trim($_POST['user_review']) : ""; // Get user review

    
    // ✅ Server-side validation for compulsory review
    // if ($user_review === "") {
    //     die("❌ Please write a review before placing your order. <a href='checkout.php'>Go back</a>");
    // }

    // ✅ Insert Order into `orders` collection in MongoDB
    try {
        $order = [
            'user_id' => new MongoDB\BSON\ObjectId($_SESSION["user_id"]),
            'total_price' => $total_price,
            'order_date' => new MongoDB\BSON\UTCDateTime(),
            'status' => 'Pending',
            'payment_method' => $payment_method,
            'items' => [],
            'review' => $user_review // ✅ Store user review in the order
        ];

        // ✅ Insert food items into `order_items`
        foreach ($_SESSION["cart"] as $food_id => $quantity) {
            $food = $db->food_items->findOne(['_id' => new MongoDB\BSON\ObjectId($food_id)]);

            if ($food) {
                $order['items'][] = [
                    'food_id' => new MongoDB\BSON\ObjectId($food_id),
                    'quantity' => $quantity,
                    'price' => $food["price"]
                ];
            }
        }

        $result = $db->orders->insertOne($order);
        $order_id = $result->getInsertedId();

        // ✅ Clear cart after order placement
        unset($_SESSION["cart"]);

        // ✅ Redirect based on payment method
        if ($payment_method === 'upi') {
            header("Location: payment.php?order_id=$order_id&amount=$total_price");
        } else {
            header("Location: order_success.php?order_id=$order_id");
        }
        exit();

    } catch (Exception $e) {
        die("❌ Error processing order: " . $e->getMessage());
    }
}
?>
