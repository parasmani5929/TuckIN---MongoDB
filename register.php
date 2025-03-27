<?php
include 'db_connection.php';  // Include MongoDB connection

// ✅ Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ✅ Check if required form fields exist
    if (!isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['phone'], $_POST['address'])) {
        die("❌ Missing required form fields. <a href='register.html'>Try again</a>");
    }

    // Retrieve user input (trim to remove unnecessary spaces)
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    try {
        // ✅ Check if the email already exists in MongoDB
        $emailExists = $db->users->findOne(['email' => $email]);

        if ($emailExists) {
            die("❌ Email already registered. <a href='register.html'>Try again</a>");
        }

        // ✅ Insert user data into MongoDB
        $insertResult = $db->users->insertOne([
            'name' => $name,
            'email' => $email,
            'password' => $password, // Hashed password
            'phone' => $phone,
            'address' => $address
        ]);

        if ($insertResult->getInsertedCount() > 0) {
            echo "✅ Registration successful! <a href='login.html'>Login here</a>";
        } else {
            echo "❌ Error in registration. Please try again.";
        }

    } catch (Exception $e) {
        die("❌ Database error: " . $e->getMessage());
    }
} else {
    // ✅ Prevent direct access
    die("❌ Invalid request. Please submit the form properly.");
}
?>
