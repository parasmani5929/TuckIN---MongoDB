<?php
session_start();
include 'db_connection.php';

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("❌ Access denied.");
}

// ✅ Get Food ID from URL
if (!isset($_GET['id'])) {
    die("❌ Invalid request.");
}

$food_id = $_GET['id'];

// ✅ Delete Food Item
try {
    $result = $db->food_items->deleteOne(['_id' => new MongoDB\BSON\ObjectId($food_id)]);

    if ($result->getDeletedCount() > 0) {
        echo "✅ Food item deleted successfully! <a href='manage_food.php'>Go back</a>";
    } else {
        echo "❌ Food item not found! <a href='manage_food.php'>Go back</a>";
    }
} catch (Exception $e) {
    die("❌ Error deleting food: " . $e->getMessage());
}
?>
