<?php
session_start();
include 'db_connection.php';  // Include MongoDB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ✅ Check if user exists in MongoDB
    $user = $db->users->findOne(['email' => $email]);

    if ($user && password_verify($password, $user['password'])) {
        // ✅ Set session variables for logged-in user
        $_SESSION['user_id'] = (string) $user['_id']; // Convert ObjectId to string
        $_SESSION['name'] = $user['name'];
        header("Location: index.php"); // Redirect to homepage
        exit;
    } else {
        echo "Invalid email or password. <a href='login.html'>Try again</a>";
    }
}
?>
