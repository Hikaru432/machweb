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

               // Handle image upload
            if ($payment_method == 'gcash' && isset($_FILES['paymentImage']) && $_FILES['paymentImage']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploaded_img';
                $filename = $_FILES['paymentImage']['name'];
                $tmp_name = $_FILES['paymentImage']['tmp_name'];
                move_uploaded_file($tmp_name, $upload_dir . '/' . $filename);

                // Update the order record with the filename
                $update_order_query = "UPDATE orders SET paymentimg = '$filename' WHERE id = $order_id";
                mysqli_query($conn, $update_order_query);
            }
        
                // Handle optional details
                $optionalDetail = $_POST['optionalDetail'] ?? ''; // Get optional detail from the form
                $cashOptionalDetail = $_POST['cashOptionalDetail'] ?? ''; // Get cash optional detail from the form
                $details = ($payment_method == 'cash') ? $cashOptionalDetail : $optionalDetail;
        
                // Update the order record with the optional detail
                $update_order_query = "UPDATE orders SET detail = '$details' WHERE id = $order_id";
                mysqli_query($conn, $update_order_query);
        

        // Clear the cart after successful order placement
        unset($_SESSION['cart']);

        // Display modal based on payment method
        if ($payment_method == 'cash') {
            // Show cash modal
            echo '<div class="modal" id="cashModal" tabindex="-1" role="dialog" aria-labelledby="cashModalLabel" aria-hidden="true">';
            echo '  <div class="modal-dialog" role="document">';
            echo '      <div class="modal-content">';
            echo '          <div class="modal-header">';
            echo '              <h5 class="modal-title" id="cashModalLabel">Cash Payment</h5>';
            echo '              <button type="button" class="close" data-dismiss="modal" aria-label="Close">';
            echo '                  <span aria-hidden="true">&times;</span>';
            echo '              </button>';
            echo '          </div>';
            echo '          <div class="modal-body">';
            echo '              <p>Home Address: ' . $user_row['homeaddress'] . '</p>';
            echo '              <form action="submit_cash_payment.php" method="post">';
            echo '                  <div class="form-group">';
            echo '                      <label for="optionalDetail">Enter other detail (optional):</label>';
            echo '                      <input type="text" class="form-control" id="optionalDetail" name="optionalDetail">';
            echo '                  </div>';
            echo '                  <input type="hidden" name="order_id" value="' . $order_id . '">';
            echo '                  <input type="hidden" name="user_id" value="' . $user_id . '">';
            echo '                  <button type="submit" class="btn btn-primary">Submit</button>';
            echo '              </form>';
            echo '          </div>';
            echo '      </div>';
            echo '  </div>';
            echo '</div>';
            echo '<script>$("#cashModal").modal("show");</script>';
            echo '<script>alert("Transaction successful! Thank you for your purchase.");</script>';
        } elseif ($payment_method == 'gcash') {
            // Show GCash modal
            echo '<div class="modal" id="gcashModal" tabindex="-1" role="dialog" aria-labelledby="gcashModalLabel" aria-hidden="true">';
            echo '  <div class="modal-dialog" role="document">';
            echo '      <div class="modal-content">';
            echo '          <div class="modal-header">';
            echo '              <h5 class="modal-title" id="gcashModalLabel">GCash Payment</h5>';
            echo '              <button type="button" class="close" data-dismiss="modal" aria-label="Close">';
            echo '                  <span aria-hidden="true">&times;</span>';
            echo '              </button>';
            echo '          </div>';
            echo '          <div class="modal-body">';
            echo '              <p>Please proceed with your payment using GCash to the following number: [INSERT GCASH NUMBER]</p>';
            echo '              <p>After payment, kindly upload a screenshot or photo of the transaction as proof.</p>';
            echo '              <form action="submit_gcash_payment.php" method="post" enctype="multipart/form-data">';
            echo '                  <div class="form-group">';
            echo '                      <label for="paymentImage">Upload Payment Screenshot:</label>';
            echo '                      <input type="file" class="form-control-file" id="paymentImage" name="paymentImage" required>';
            echo '                  </div>';
            echo '                  <input type="hidden" name="order_id" value="' . $order_id . '">';
            echo '                  <input type="hidden" name="user_id" value="' . $user_id . '">';
            echo '                  <button type="submit" class="btn btn-primary">Submit</button>';
            echo '              </form>';
            echo '          </div>';
            echo '      </div>';
            echo '  </div>';
            echo '</div>';
            echo '<script>$("#gcashModal").modal("show");</script>';
            echo '<script>alert("Transaction successful! Thank you for your purchase.");</script>';
        } elseif ($payment_method == 'paypal') {
            // Show PayPal modal
            // You can implement a similar modal for PayPal here
        }
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

    <br>
    <br>


    <!-- User Information and Selected Parts Grid -->
    <div class="row">
        <!-- User Information -->
        <div class="col-md-6">
            <h3>User Information</h3>
            <p>Name: <?php echo $user_row['name']; ?></p>
            <p>Email: <?php echo $user_row['email']; ?></p>
            <p>Barangay: <?php echo $user_row['barangay']; ?></p>
            <p>Municipality: <?php echo $user_row['municipality']; ?></p>
        </div>

        <!-- Selected Parts -->
        <div class="col-md-6">
        <div class="card-body">
            <h3 class="card-title">Estimated Parts Price</h3>
            <div class="table-responsive">
                <table class="table table-bordered" style="font-size: 14px;">
                    <thead>
                        <tr>
                            <th>Checkbox Value</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="partsTableBody">
                        <?php 
                        // Initialize overall total
                        $overallTotal = 0;

                        // Fetch selected checkboxes information from the selected_checkboxes table
                        $selected_query = "SELECT * FROM selected_checkboxes WHERE user_id = $user_id";
                        $selected_result = mysqli_query($conn, $selected_query);
                        while ($selected_row = mysqli_fetch_assoc($selected_result)):
                            // Calculate total for each selected part
                            $total = $selected_row['quantity'] * $selected_row['price'];
                            $overallTotal += $total; // Add to overall total
                        ?>
                        <tr>
                            <td><?php echo $selected_row['checkbox_value']; ?></td>
                            <td><?php echo $selected_row['quantity']; ?></td>
                            <td><?php echo $selected_row['price']; ?></td>
                            <td><?php echo $total; ?></td> <!-- Display total for each part -->
                        </tr>
                        <?php endwhile; ?>
                        <tr>
                            <td colspan="3" class="text-right" style="font-weight: 700;">Overall Total:</td>
                            <td style="font-weight: 700;"><?php echo $overallTotal; ?></td> <!-- Display overall total -->
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    </div>

    <br>
    <br>


    <!-- Cart Contents -->
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

    <!-- Payment Method -->
<div class="container mt-4">
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
        <div id="gcash-details" style="display: none;">
            <div class="form-group">
                <label for="paymentImage">Upload Payment Screenshot:</label>
                <input type="file" class="form-control-file" id="paymentImage" name="paymentImage" required>
            </div>
            <div class="form-group">
                <label for="optionalDetail">Enter other detail (optional):</label>
                <input type="text" class="form-control" id="optionalDetail" name="optionalDetail">
            </div>
        </div>
        <!-- Display user's home address for Cash payment -->
        <div id="cash-details" style="display: block;">
            <p>Home Address: <?php echo $user_row['homeaddress']; ?></p>
            <div class="form-group">
                <label for="cashOptionalDetail">Enter other detail (optional):</label>
                <input type="text" class="form-control" id="cashOptionalDetail" name="cashOptionalDetail">
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Place Order</button>
    </form>
</div>

<script>
    // Function to show GCash details when selected
    function showGCashDetails() {
        document.getElementById("gcash-details").style.display = "block";
        document.getElementById("cash-details").style.display = "none"; // Hide Cash details
    }

    // Function to show Cash details when selected
    function showCashDetails() {
        document.getElementById("cash-details").style.display = "block";
        document.getElementById("gcash-details").style.display = "none"; // Hide GCash details
    }

    // Event listener for GCash radio button
    document.getElementById("gcash").addEventListener("change", function() {
        if (this.checked) {
            showGCashDetails();
        }
    });

    // Event listener for Cash radio button
    document.getElementById("cash").addEventListener("change", function() {
        if (this.checked) {
            showCashDetails();
        }
    });

    // Check the default selected radio button and display details accordingly
    if (document.getElementById("gcash").checked) {
        showGCashDetails();
    } else {
        showCashDetails();
    }
</script>

</div>
</body>
</html>
