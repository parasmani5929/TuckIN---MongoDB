<?php
session_start();
require 'db_connection.php'; // MongoDB connection

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("❌ Access denied.");
}

// ✅ Check if order_id is received
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["order_id"])) {
    $order_id = $_POST["order_id"];

    // ✅ Convert order_id to MongoDB ObjectId
    $order_id = new MongoDB\BSON\ObjectId($order_id);

    // ✅ Update order status to "Completed"
    $result = $ordersCollection->updateOne(
        ['_id' => $order_id], // Find order by ID
        ['$set' => ['status' => 'Completed']] // Update status to "Completed"
    );

    // ✅ Redirect back to manage orders page
    header("Location: manage_orders.php");
    exit;
} else {
    die("❌ Invalid request.");
}
?>
