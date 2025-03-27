<?php
require 'vendor/autoload.php'; // Include MongoDB library

use MongoDB\Client;

try {
    // Connect to MongoDB
    $client = new Client("mongodb://localhost:27017");
    $db = $client->food_ordering; // Change 'food_ordering' to your DB name
    echo "Connected to MongoDB successfully!";
} catch (Exception $e) {
    echo "Failed to connect: " . $e->getMessage();
}
?>
