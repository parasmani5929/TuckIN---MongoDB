<?php
session_start();
include 'db_connection.php';  // Include MongoDB connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['name'], $_POST['email'], $_POST['password'])) {
        die("❌ Missing required fields. <a href='adminregister.html'>Try again</a>");
    }

    // Retrieve user input (trim to remove unnecessary spaces)
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing

    try {
        // ✅ Check if email already exists in MongoDB
        $emailExists = $db->admin->findOne(['email' => $email]);

        if ($emailExists) {
            die("❌ Email already registered. <a href='adminregister.html'>Try again</a>");
        }

        // ✅ Insert admin data into MongoDB
        $insertResult = $db->admin->insertOne([
            'name' => $name,
            'email' => $email,
            'password' => $password // Hashed password
        ]);

        if ($insertResult->getInsertedCount() > 0) {
            echo "✅ Admin registered successfully! <a href='adminlogin.html'>Login here</a>";
        } else {
            echo "❌ Error in registration.";
        }

    } catch (Exception $e) {
        die("❌ Database error: " . $e->getMessage());
    }
} else {
    header("Location: adminregister.html"); // Redirect if accessed without form submission
    exit;
}
?>
