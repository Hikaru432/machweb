<?php
session_start();
include 'config.php';

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM user WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user_row = mysqli_fetch_assoc($user_result);

// Fetch products from the database based on the cart
$cart = $_SESSION['cart'];
$products = [];
$totalPrice = 0;

foreach ($cart as $product_id => $quantity) {
    $product_query = "SELECT * FROM products WHERE id = $product_id";
    $product_result = mysqli_query($conn, $product_query);
    $product_row = mysqli_fetch_assoc($product_result);
    $product_row['quantity'] = $quantity;
    $totalPrice += ($product_row['selling_price'] * $quantity);
    $products[] = $product_row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $payment_method = $_POST['payment_method']; // Assuming radio button values are 'cash', 'gcash', 'paypal'

    // Insert order details into the database
    $insert_order_query = "INSERT INTO orders (user_id, payment_method, total_price) VALUES ('$user_id', '$payment_method', '$totalPrice')";
    $insert_order_result = mysqli_query($conn, $insert_order_query);

    if ($insert_order_result) {
        $order_id = mysqli_insert_id($conn); // Get the ID of the inserted order

        // Insert order items into the database
        foreach ($products as $product) {
            $product_id = $product['id'];
            $quantity = $product['quantity'];
            $insert_order_item_query = "INSERT INTO order_items (order_id, product_id, quantity) VALUES ('$order_id', '$product_id', '$quantity')";
            mysqli_query($conn, $insert_order_item_query);
        }

        // Clear the cart after successful order placement
        unset($_SESSION['cart']);

        // Redirect to a thank you page or order confirmation page
        header('Location: order_confirmation.php');
        exit();
    } else {
        // Handle insertion failure
        $error_message = "Failed to place the order. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #b30036;">
    <div class="container">
        <a class="navbar-brand text-white" href="home.php">MachPH Store</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="home.php">Home</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link text-white" href="shop.php">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Contact</a>
                </li>
                <!-- Cart icon -->
                    <ul class="navbar-nav ml-2">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#" data-toggle="modal" data-target="#cartModal">
                                <i class="fas fa-shopping-cart"></i> Cart <span class="badge badge-pill badge-light"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
                            </a>
                        </li>
                    </ul>
            </ul>
          <!-- Search Bar -->
          <form class="form-inline my-2 my-lg-0" method="GET" action="shop.php">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="search">
                <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2>Checkout</h2>
    <?php if(isset($error_message)): ?>
   
        <div class="alert alert-danger" role="alert">
        <?php echo $error_message; ?>
    </div>
    <?php endif; ?>
    <h3>User Information</h3>
    <p>Name: <?php echo $user_row['name']; ?></p>
    <p>Email: <?php echo $user_row['email']; ?></p>
    <p>Barangay: <?php echo $user_row['barangay']; ?></p>
    <p>Municipality: <?php echo $user_row['municipality']; ?></p>

    <h3>Cart Contents</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo $product['item_name']; ?></td>
                <td>₱<?php echo number_format($product['selling_price'], 2); ?></td>
                <td><?php echo $product['quantity']; ?></td>
                <td>₱<?php echo number_format($product['selling_price'] * $product['quantity'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="text-right">Total:</td>
                <td>₱<?php echo number_format($totalPrice, 2); ?></td>
            </tr>
        </tbody>
    </table>

    <h3>Payment Method</h3>
    <form method="post">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" checked>
            <label class="form-check-label" for="cash">Cash</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="payment_method" id="gcash" value="gcash">
            <label class="form-check-label" for="gcash">GCash</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
            <label class="form-check-label" for="paypal">PayPal</label>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Place Order</button>
    </form>
</div>
</body>
</html>
