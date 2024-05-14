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

        // Handle image upload and optional details based on payment method
        if ($payment_method == 'gcash' && isset($_FILES['paymentImage']) && $_FILES['paymentImage']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploaded_img';
            $filename = $_FILES['paymentImage']['name'];
            $tmp_name = $_FILES['paymentImage']['tmp_name'];
            move_uploaded_file($tmp_name, $upload_dir . '/' . $filename);

            // Update the order record with the filename
            $update_order_query = "UPDATE orders SET paymentimg = '$filename' WHERE id = $order_id";
            mysqli_query($conn, $update_order_query);
        }
        
        $currentDate = date('Y-m-d'); // Get current date
        foreach ($products as $product) {
            $product_id = $product['id'];
            $quantity = $product['quantity'];
            $price = $product['selling_price']; // Assuming 'selling_price' is the column name for product price
            $insert_order_item_query = "INSERT INTO order_items (order_id, product_id, quantity, price, order_date) VALUES ('$order_id', '$product_id', '$quantity', '$price', '$currentDate')";
            mysqli_query($conn, $insert_order_item_query);
        }


        // Handle optional details
        $optionalDetail = $_POST['optionalDetail'] ?? ''; // Get optional detail from the form
        $cashOptionalDetail = $_POST['cashOptionalDetail'] ?? ''; // Get cash optional detail from the form
        $details = ($payment_method == 'cash') ? $cashOptionalDetail : $optionalDetail;

        // If $details is empty, set it to NULL
        if (empty($details)) {
            $details = null;
        }

        // Update the order record with the optional detail
        $update_order_query = "UPDATE orders SET detail = ? WHERE id = ?";
        $update_statement = mysqli_prepare($conn, $update_order_query);
        mysqli_stmt_bind_param($update_statement, "si", $details, $order_id);
        mysqli_stmt_execute($update_statement);

        // Clear the cart after successful order placement
        unset($_SESSION['cart']);

        // Display modal based on payment method
        if ($payment_method == 'cash') {
            // Show cash modal
            // Your modal code remains the same...
        } elseif ($payment_method == 'gcash') {
            // Show GCash modal
            // Your modal code remains the same...
        } elseif ($payment_method == 'paypal') {
            // Show PayPal modal
            // Your modal code remains the same...
        }
        echo '<script>alert("Thank you for your purchase!"); window.location.href = "shop.php";</script>';
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Checkout</h2>
        <div class="row">
            <div class="col-md-6">
                <h4>Order Summary</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                        <tr>
                            <td><?php echo $product['item_name']; ?></td>
                            <td><?php echo $product['quantity']; ?></td>
                            <td>₱<?php echo number_format($product['selling_price'], 2); ?></td>
                            <td>₱<?php echo number_format($product['selling_price'] * $product['quantity'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total:</th>
                            <th>₱<?php echo number_format($totalPrice, 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-6">
                <h4>Payment Method</h4>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="paymentMethod">Select Payment Method:</label>
                        <select class="form-control" id="paymentMethod" name="payment_method">
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                    <div id="paymentImageDiv" style="display: none;">
                        <div class="form-group">
                            <label for="paymentImage">Upload Payment Screenshot:</label>
                            <input type="file" class="form-control-file" id="paymentImage" name="paymentImage">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="optionalDetail">Enter other detail (optional):</label>
                        <input type="text" class="form-control" id="optionalDetail" name="optionalDetail">
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit_payment">Submit Payment</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('paymentMethod').addEventListener('change', function() {
            var paymentMethod = this.value;
            var paymentImageDiv = document.getElementById('paymentImageDiv');
            if (paymentMethod === 'gcash') {
                paymentImageDiv.style.display = 'block';
            } else {
                paymentImageDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html>
