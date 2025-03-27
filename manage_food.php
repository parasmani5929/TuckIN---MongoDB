<?php
session_start();
include 'db_connection.php';

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("❌ Access denied. <a href='adminlogin.php'>Admin Login</a>");
}

// ✅ Fetch Food Items from MongoDB
$foods = $db->food_items->find([], ['sort' => ['_id' => -1]]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Food Items</title>
    <link rel="stylesheet" href="css/styles1.css">
</head>
<body>

<h1>Manage Food Items</h1>

<!-- ✅ List of Food Items -->
<h2>Food List</h2>
<table border="1">
    <tr>
        <th>Food ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Category</th>
        <th>Image</th>
        <th>Action</th>
    </tr>
    <?php foreach ($foods as $food): ?>
        <tr>
            <td><?php echo $food['_id']; ?></td> <!-- MongoDB uses _id instead of food_id -->
            <td><?php echo $food['name']; ?></td>
            <td><?php echo $food['description']; ?></td>
            <td>₹<?php echo $food['price']; ?></td>
            <td><?php echo $food['category']; ?></td>
            <td><img src="<?php echo $food['image']; ?>" width="50"></td>
            <td>
                <a href="edit_food.php?id=<?php echo $food['_id']; ?>">✏️Edit</a>
                <a href="delete_food.php?id=<?php echo $food['_id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- ✅ Add New Food Item -->
<h2>➕ Add New Food</h2>
<form action="add_food.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Food Name" required>
    <input type="text" name="description" placeholder="Description" required>
    <input type="number" name="price" placeholder="Price" step="0.01" required>
    <input type="text" name="category" placeholder="Category" required>
    <input type="file" name="image" required>
    <button type="submit">Add Food</button>
</form>

<a href="admin_dashboard.php">Back to Dashboard</a>

</body>
</html>
