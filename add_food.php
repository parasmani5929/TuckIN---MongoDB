<?php
session_start();
include 'db_connection.php';  // Include MongoDB connection

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("❌ Access denied. <a href='adminlogin.php'>Admin Login</a>");
}

// ✅ Handle form submission to add food item
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['name'], $_POST['description'], $_POST['price'], $_POST['category']) || !isset($_FILES['image'])) {
        die("❌ Missing required fields. <a href='manage_food.php'>Try again</a>");
    }

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']); // Convert price to float
    $category = trim($_POST['category']);
    $image = $_FILES['image'];

    // ✅ Validate and upload image
    $target_dir = "uploads/"; // Ensure this folder is writable
    $target_file = $target_dir . basename($image["name"]);
    
    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        $image_path = $target_file;
    } else {
        die("❌ Failed to upload image.");
    }

    // ✅ Insert food item into MongoDB
    try {
        $db->food_items->insertOne([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category' => $category,
            'image' => $image_path
        ]);

        echo "✅ Food item added successfully! <a href='manage_food.php'>Go back to manage food</a>";
    } catch (Exception $e) {
        die("❌ Error inserting food item: " . $e->getMessage());
    }
} else {
    die("❌ Invalid request. <a href='manage_food.php'>Go back</a>");
}
?>
