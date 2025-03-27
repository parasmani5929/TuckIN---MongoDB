<?php
session_start();
include 'db_connection.php';

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("❌ Access denied. <a href='adminlogin.php'>Admin Login</a>");
}

// ✅ Fetch Orders with User & Food Details
$ordersCursor = $db->orders->find([], ['sort' => ['order_date' => -1]]);
$orders = iterator_to_array($ordersCursor);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <style>
        body {
            background-color: #121212; /* Dark background */
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        table {
            width: 85%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #1e1e1e; /* Slightly lighter dark color for contrast */
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 2px solid #333;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #333;
        }
        td {
            background-color: #222;
        }
        a {
            color: #00c3ff;
            font-size: 18px;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h1>Manage Orders</h1>
<table>
    <tr>
        <th>Order ID</th>
        <th>User</th>
        <th>Food Items</th>
        <th>Total Quantity</th>
        <th>Total Price</th>
        <th>Status</th>
    </tr>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo $order['_id']; ?></td>
            <td>
                <?php 
                $user = $db->users->findOne(['_id' => $order['user_id']]);
                echo $user ? htmlspecialchars($user['name']) : 'Unknown User'; 
                ?>
            </td>
            <td>
                <?php 
                $foodNames = [];
                foreach ($order['items'] as $item) {
                    $food = $db->food_items->findOne(['_id' => $item['food_id']]);
                    if ($food) {
                        $foodNames[] = htmlspecialchars($food['name']);
                    }
                }
                echo implode(", ", $foodNames);
                ?>
            </td>
            <td>
                <?php 
                $itemsArray = json_decode(json_encode($order['items']), true);
                $totalQuantity = array_sum(array_column($itemsArray, 'quantity'));
                echo $totalQuantity;
                ?>
            </td>
            <td>₹<?php echo $order['total_price']; ?></td>
            <td>
                <?php echo ($order['payment_method'] === 'cod') ? 'COD' : 'Paid'; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<br>
<a href="admin_dashboard.php">⬅️ Back to Dashboard</a>

</body>
</html>
