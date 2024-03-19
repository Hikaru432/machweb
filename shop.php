<?php
session_start();
include 'config.php';

// Fetch products from the database
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);

// Check if user is logged in, if not redirect to login page
if(!isset($_SESSION['user_id'])){
   header('location:login.php');
   exit();
}

// Handle adding items to cart
if(isset($_POST['add_to_cart'])){
    // Retrieve product details
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    // Add item to cart session variable
    if(!isset($_SESSION['cart'][$product_id]))
        $_SESSION['cart'][$product_id] = 0;
    $_SESSION['cart'][$product_id] += $quantity;
    
    // Redirect back to shop page
    header('Location: shop.php');
    exit();
}

// Handle editing product quantity
if(isset($_POST['update_quantity'])){
    $product_id = $_POST['product_id'];
    $new_quantity = $_POST['new_quantity'];
    
    // Update quantity in cart session variable
    $_SESSION['cart'][$product_id] = $new_quantity;
    
    // Redirect back to shop page
    header('Location: shop.php');
    exit();
}

// Handle deleting product from cart
if(isset($_POST['delete_product'])){
    $product_id = $_POST['product_id'];
    
    // Remove product from cart session variable
    unset($_SESSION['cart'][$product_id]);
    
    // Redirect back to shop page
    header('Location: shop.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Add custom styles here */
    </style>
</head>
<body>
<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Your Shop</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="#">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">About</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="shop.php">Shop</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Contact</a>
            </li>
        </ul>
        <!-- Cart icon -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#" data-toggle="modal" data-target="#cartModal">
                    <i class="fas fa-shopping-cart"></i> Cart <span class="badge badge-pill badge-primary"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
                </a>
            </li>
        </ul>
    </div>
</nav>


<!-- Main content -->
<div class="container mt-5">
    <h2>Shop</h2>
    <div class="row">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="<?php echo $row['product_image']; ?>" class="card-img-top" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['item_name']; ?></h5>
                        <p class="card-text"><?php echo $row['selling_price']; ?></p>
                        <form action="shop.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" class="form-control mb-2">
                            <button type="submit" name="add_to_cart" class="btn btn-primary btn-block" onclick="showAlert()">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Cart</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Cart content here -->
                <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($_SESSION['cart'] as $product_id => $quantity): ?>
                                <?php
                                    // Fetch product details from products table
                                    $product_query = "SELECT * FROM products WHERE id = $product_id";
                                    $product_result = mysqli_query($conn, $product_query);
                                    $product_row = mysqli_fetch_assoc($product_result);
                                    $product_name = $product_row['item_name'];
                                ?>
                                <tr>
                                    <td><?php echo $product_name; ?></td>
                                    <td>
                                        <form action="shop.php" method="post">
                                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                            <input type="number" name="new_quantity" value="<?php echo $quantity; ?>" min="1" class="form-control mb-2">
                                            <button type="submit" name="update_quantity" class="btn btn-primary">Update</button>
                                        </form>
                                    </td>
                                    <td>
                                        <form action="shop.php" method="post">
                                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                            <button type="submit" name="delete_product" class="btn btn-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Your cart is empty.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function showAlert() {
        alert('Item added to cart!');
    }
</script>
</body>
</html>
