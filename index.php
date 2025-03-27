<?php
session_start();
include 'db_connection.php';

// ✅ Get food items collection
$foodCollection = $db->food_items;

// ✅ Fetch food items from MongoDB
$food_items = $foodCollection->find()->toArray();

// ✅ Check if a search query exists
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
    $food_items = $foodCollection->find([
        'name' => new MongoDB\BSON\Regex($searchQuery, 'i') // Case-insensitive search
    ])->toArray();
}

// ✅ Handle Add to Cart
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["food_id"], $_POST["quantity"])) {
    $food_id = new MongoDB\BSON\ObjectId($_POST["food_id"]);
    $quantity = (int) $_POST["quantity"];

    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = [];
    }

    if (isset($_SESSION["cart"][(string)$food_id])) {
        $_SESSION["cart"][(string)$food_id] += $quantity;
    } else {
        $_SESSION["cart"][(string)$food_id] = $quantity;
    }

    // Refresh page to update cart instantly
    header("Location: index.php");
    exit;
}

// ✅ Handle Remove from Cart
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["remove_food_id"])) {
    $remove_food_id = $_POST["remove_food_id"];
    unset($_SESSION["cart"][$remove_food_id]);

    // Refresh page after removing item
    header("Location: index.php");
    exit;
}

// ✅ Get Cart Items
$cart_items = [];
if (!empty($_SESSION["cart"])) {
    foreach ($_SESSION["cart"] as $food_id => $quantity) {
        $food = $foodCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($food_id)]);
        if ($food) {
            $food["quantity"] = $quantity;
            $cart_items[] = $food;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Food Ordering</title>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="css/index.css">
</head>
<body>

<section class="banner">
    <h1>Tuck In <span>On Time</span></h1>
</section>

<header>
    <div class="logo">
        <img src="logo.jpg" alt="TuckIn Logo" width="120" height="120">
    </div>
    <nav>
        <ul>
            <li><a href="about.html" target="_blank">About us</a></li>
            <li><a href="contact.html">Contact us</a></li>
        </ul>
    </nav>

    <div class="search-bar">
        <form action="index.php" method="GET">
            <input type="text" name="search" placeholder="Search Food" value="<?php echo htmlspecialchars($searchQuery); ?>">
        </form>
    </div>

    <div class="auth-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>👤 Welcome, <?php echo $_SESSION['name']; ?>!</span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="forget-pass.php">Forget password</a>
            <a href="register.html">Register</a>
            <a href="login.html">Login</a>
        <?php endif; ?>
        <!-- Cart Button -->
        <button id="cart-btn" style="background-color: #ff5722; color: #fff; border: none; padding: 10px 20px; border-radius: 30px; cursor: pointer;">🛒 Cart</button>
    </div>
</header>

<!-- Cart Modal -->
<div id="cart-modal" class="cart-modal">
    <div class="cart-content">
        <h1>🛒 Your Cart</h1>
        <?php if (!empty($cart_items)): ?>
            <ul>
                <?php foreach ($cart_items as $item): ?>
                    <li>
                        <img src="<?php echo $item['image']; ?>" width="70">
                        <?php echo $item['name']; ?> - ₹<?php echo $item['price']; ?> x <?php echo $item['quantity']; ?>
                        <form action="index.php" method="POST" style="display:inline;">
                            <input type="hidden" name="remove_food_id" value="<?php echo (string) $item['_id']; ?>">
                            <button type="submit">Remove</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="total">Total: ₹
                <?php echo array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart_items)); ?>
            </p>
            <a href="checkout.php"><button class="checkout-btn">Proceed to Checkout</button></a>
        <?php else: ?>
            <p>🛒 Your cart is empty.</p>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <!-- ✅ Food Items Section -->
    <div class="food-items">
        <?php if (empty($food_items)): ?>
            <p>❌ No results found for "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"</p>
        <?php else: ?>
            <?php foreach ($food_items as $food): ?>
                <div class="food-card">
                    <img src="<?php echo $food['image']; ?>" alt="<?php echo $food['name']; ?>">
                    <h3><?php echo $food['name']; ?></h3>
                    <p><?php echo $food['description']; ?></p>
                    <p class="price">₹<?php echo $food['price']; ?></p>
                    <form action="index.php" method="POST">
                        <input type="hidden" name="food_id" value="<?php echo $food['_id']; ?>">
                        <input type="number" name="quantity" value="1" min="1">
                        <button type="submit" class="btn">Add to Cart</button>
                    </form>

                    <!-- Rating Section -->
                    <div class="rating">
                        <form action="rate_food.php" method="POST">
                            <input type="hidden" name="food_id" value="<?php echo $food['_id']; ?>">
                            <label for="rating">Rate this item:</label>
                            <select name="rating" required>
                                <option value="">Select Rating</option>
                                <option value="1">1 Star</option>
                                <option value="2">2 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="5">5 Stars</option>
                            </select>
                            <button type="submit" class="btn">Submit</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="admin-login">
    <a href="adminlogin.html">Admin login</a>
</div>

<script>
    // Show/Hide Cart Modal
    document.getElementById('cart-btn').addEventListener('click', function() {
        var modal = document.getElementById('cart-modal');
        modal.style.display = modal.style.display === 'flex' ? 'none' : 'flex';
    });

    // Close the modal when clicked outside of the content
    window.addEventListener('click', function(event) {
        var modal = document.getElementById('cart-modal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
</script>

</body>
</html>
