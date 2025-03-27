<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["food_id"], $_POST["rating"])) {
    $food_id = new MongoDB\BSON\ObjectId($_POST["food_id"]);
    $rating = (int)$_POST["rating"];

    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please log in to rate items.'); window.location.href='login.html';</script>";
        exit;
    }

    $user_id = new MongoDB\BSON\ObjectId($_SESSION["user_id"]);

    // Get user details to retrieve the name
    $userCollection = $db->users;
    $user = $userCollection->findOne(['_id' => $user_id]);

    if (!$user) {
        echo "<script>alert('User not found!'); window.location.href='index.php';</script>";
        exit;
    }

    $name = $user['name']; // Assuming the user's name is stored in the 'name' field in the users collection

    $ratingCollection = $db->food_ratings;

    // Check if the user already rated this item
    $existingRating = $ratingCollection->findOne([
        'food_id' => $food_id,
        'user_id' => $user_id
    ]);

    if ($existingRating) {
        // Update existing rating with the name
        $ratingCollection->updateOne(
            ['_id' => $existingRating['_id']],
            ['$set' => ['rating' => $rating, 'name' => $name]]
        );
    } else {
        // Insert new rating with the name included
        $ratingCollection->insertOne([
            'food_id' => $food_id,
            'user_id' => $user_id,
            'rating' => $rating,
            'name' => $name // Store the name here
        ]);
    }

    // Display a message with the name and rating
    echo "<script>alert('Thank you, $name, for rating the food! Your rating: $rating stars.'); window.location.href='index.php';</script>";
}
?>
