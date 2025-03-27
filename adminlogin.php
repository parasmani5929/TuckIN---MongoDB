<?php
session_start();
include 'db_connection.php';  // Include MongoDB connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['email'], $_POST['password'])) {
        die("❌ Missing email or password. <a href='adminlogin.php'>Try again</a>");
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        // ✅ Fetch admin details from MongoDB
        $admin = $db->admin->findOne(['email' => $email]);

        if ($admin && password_verify($password, $admin['password'])) {
            // ✅ Store admin details in session
            $_SESSION['admin_id'] = $admin['_id'];  // MongoDB uses `_id` instead of `admin_id`
            $_SESSION['admin_name'] = $admin['name'];  // Ensure the field name is correct

            header("Location: admin_dashboard.php"); // Redirect to admin dashboard
            exit;
        } else {
            echo "❌ Invalid email or password. <a href='adminlogin.php'>Try again</a>";
        }
    } catch (Exception $e) {
        die("❌ Database error: " . $e->getMessage());
    }
} else {
    header("Location: adminlogin.html"); // Redirect if accessed without form submission
    exit;
}
?>
