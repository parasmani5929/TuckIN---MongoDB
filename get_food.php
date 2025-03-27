<?php
include 'db_connection.php'; // Include MongoDB connection

// Fetch all food items from MongoDB
$foods = $db->food_items->find();

// Display food items dynamically
foreach ($foods as $food) {
    echo "<div class='food-card'>";
    echo "<img src='images/" . $food['image'] . "' alt='" . $food['name'] . "'>";
    echo "<h3>" . $food['name'] . "</h3>";
    echo "<p>" . $food['description'] . "</p>";
    echo "<p class='price'>₹" . $food['price'] . "</p>";
    echo "<button onclick='addToCart(\"" . $food['_id'] . "\")'>Add to Cart</button>"; // Use `_id` for MongoDB
    echo "</div>";
}
?>
