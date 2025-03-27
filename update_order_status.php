<?php
session_start();
require 'db_connection.php'; // MongoDB connection

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("❌ Access denied. <a href='adminlogin.php'>Admin Login</a>");
}

// ✅ Ensure required data is received
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["order_id"], $_POST["new_status"])) {
    $order_id = $_POST["order_id"];
    $new_status = $_POST["new_status"];

    // ✅ Convert order_id to MongoDB ObjectId
    $order_id = new MongoDB\BSON\ObjectId($order_id);

    // ✅ Update order status in MongoDB
    $result = $ordersCollection->updateOne(
        ['_id' => $order_id], // Find order by ID
        ['$set' => ['status' => $new_status]] // Update status field
    );

    // ✅ Check if the update was successful
    if ($result->getModifiedCount() > 0) {
        echo "✅ Order status updated successfully!";
    } else {
        echo "❌ Failed to update order status.";
    }
}

// ✅ Redirect back to manage orders page
header("Location: manage_orders.php");
exit;
?>
