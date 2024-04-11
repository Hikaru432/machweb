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

// Fetch companyid from the session or URL parameter
$companyid = isset($_GET['companyid']) ? $_GET['companyid'] : (isset($_SESSION['companyid']) ? $_SESSION['companyid'] : null);

// Display company ID (you may remove this line if not needed)
// echo "Company ID: " . $companyid; 

// Fetch company information based on companyid
$company_info = null; // Initialize the variable
if (!empty($companyid)) {
    $company_select = mysqli_query($conn, "SELECT companyname FROM autoshop WHERE companyid = '$companyid'");
    if ($company_row = mysqli_fetch_assoc($company_select)) {
        $company_info = $company_row; // Assign company information to $company_info variable
        // echo "Company Name: " . $company_info['companyname'];
    } else {
        echo "Company not found";
    }
}

// Fetch products from the database based on companyid
if (!empty($companyid)) {
    $query = "SELECT * FROM products WHERE companyid = $companyid";
} else {
    // If companyid is not provided, fetch all products
    $query = "SELECT * FROM products";
}


// Fetch products from the database based on search query
if(isset($_GET['search']) && !empty($_GET['search'])){
    $search = $_GET['search'];
    $query = "SELECT * FROM products WHERE item_name LIKE '%$search%'";
} else {
    // If no search query, fetch all products
    $query = "SELECT * FROM products";
}
$result = mysqli_query($conn, $query);

// Code for system redirection
if (isset($_GET['system']) && !empty($_GET['system'])) {
    $system = $_GET['system'];
    $query = "SELECT * FROM products WHERE system = '$system'";
    $result = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom Styles */
        .card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .navbar-brand {
            font-weight: 800;
        }
    </style>
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

    <!-- <div class="row mb-2">
        <div class="col-md-12 text-center">
            <a href="shop.php?system=Engine" class="btn btn-primary mr-2">Engine System</a>
            <a href="shop.php?system=Maintenance" class="btn btn-primary">Maintenance System</a>
        </div>
    </div> -->

<!-- Main content -->
<div class="container mt-4">
    <div class="row">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="<?php echo $row['product_image']; ?>" class="card-img-top" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['item_name']; ?></h5>
                        <p class="card-text"><strong>Price:</strong> <span style="padding: 4px;">₱</span><?php echo $row['selling_price']; ?></p>
                        <?php
                        // Fetch company name based on companyid
                        $companyid = $row['companyid'];
                        $company_query = mysqli_query($conn, "SELECT companyname FROM autoshop WHERE companyid = '$companyid'");
                        $company_row = mysqli_fetch_assoc($company_query);
                        $companyname = $company_row['companyname'];
                        ?>
                        <p class="card-text"><strong>Company:</strong> <?php echo $companyname; ?></p> <!-- Display company name here -->
                        <a href="#" class="text-primary font-italic" data-toggle="modal" data-target="#productModal<?php echo $row['id']; ?>">Product Details</a>
                        <form action="shop.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" class="form-control mb-2">
                            <button type="submit" name="add_to_cart" class="btn btn-primary btn-block">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>


<!-- Product details modals -->
<?php mysqli_data_seek($result, 0); // Reset result pointer to the beginning ?>
<?php while($row = mysqli_fetch_assoc($result)): ?>
    <div class="modal fade" id="productModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="productModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel<?php echo $row['id']; ?>">Product Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Product details -->
                    <div class="row">
                        <div class="col-md-6">
                            <img src="<?php echo $row['product_image']; ?>" class="img-fluid" alt="Product Image">
                        </div>
                        <div class="col-md-6">
                            <p><strong>Item Name:</strong> <?php echo $row['item_name']; ?></p>
                            <p><strong>Category:</strong> <?php echo $row['category']; ?></p>
                            <p><strong>Price:</strong> <span style="padding: 4px;">₱</span><?php echo $row['selling_price']; ?></p>
                            <!-- Add more details as needed -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endwhile; ?>


<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
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
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalPrice = 0;
                            foreach($_SESSION['cart'] as $product_id => $quantity): 
                                // Fetch product details from products table
                                $product_query = "SELECT * FROM products WHERE id = $product_id";
                                $product_result = mysqli_query($conn, $product_query);
                                $product_row = mysqli_fetch_assoc($product_result);
                                $product_name = $product_row['item_name'];
                                $product_price = $product_row['selling_price'];
                                $totalProductPrice = $product_price * $quantity;
                                $totalPrice += $totalProductPrice;
                            ?>
                                <tr>
                                    <td><?php echo $product_name; ?></td>
                                    <td>₱<?php echo number_format($product_price, 2); ?></td>
                                    <td><?php echo $quantity; ?></td>
                                    <td>₱<?php echo number_format($totalProductPrice, 2); ?></td>
                                    <td>
                                        <form action="shop.php" method="post">
                                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                            <button type="submit" name="delete_product" class="btn btn-danger btn-sm">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right">Total:</td>
                                <td colspan="2">₱<?php echo number_format($totalPrice, 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php else: ?>
                    <p>Your cart is empty.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <a href="checkout.php?companyid=<?php echo $companyid; ?>" class="btn btn-primary mt-3">Place Order</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="fixedContainer"  data-toggle="modal" data-target="#exampleModal" style="position: fixed; top: 100px; right: -10px; width: 70px; height: 70px; background-color: black; border-radius: 8px; z-index: 999; display: flex; align-items: center; justify-content: center;">
    <h1 style="font-weight: 800; color: white; font-size: 20px; cursor: pointer;">Parts</h1>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Estimated Parts Price</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Checkbox Value</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="partsTableBody">
                            <!-- Dynamically generated product rows will be inserted here -->
                        </tbody>
                    </table>
                </div>
                <div class="total-price" style="margin-left: 320px; font-weight: 700;">Overall Total: ₱<span id="overallTotal">0</span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="saveButton" type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // Function to fetch and populate products in the modal
    $('#exampleModal').on('show.bs.modal', function(event) {
        var modal = $(this);
        // Fetch data from the server using AJAX
        fetch('fetch_data_from_database.php')
            .then(response => response.json())
            .then(data => {
                // Clear previous product rows
                modal.find('#partsTableBody').empty();
                // Add product rows dynamically
                data.forEach(item => {
                    var totalPrice = item.quantity * item.price;
                    var productHTML = `
                        <tr>
                            <td>${item.checkbox_value}</td>
                            <td>${item.quantity}</td>
                            <td>₱${item.price}</td>
                            <td>₱${totalPrice}</td>
                        </tr>`;
                    modal.find('#partsTableBody').append(productHTML);
                });
                // Calculate overall total price
                calculateOverallTotal();
            })
            .catch(error => console.error('Error:', error));
    });

    // Calculate overall total price
    function calculateOverallTotal() {
        var modal = $('#exampleModal');
        var overallTotal = 0;
        modal.find('#partsTableBody tr').each(function() {
            overallTotal += parseInt($(this).find('td:eq(3)').text().replace('₱', ''));
        });
        $('#overallTotal').text(overallTotal);
    }

    // Save selected parts
    $('#saveButton').click(function() {
        var modal = $('#exampleModal');
        var selectedParts = [];
        modal.find('#partsTableBody tr').each(function() {
            var quantity = parseInt($(this).find('td:eq(1)').text());
            var price = parseInt($(this).find('td:eq(2)').text().replace('₱', ''));
            selectedParts.push({
                checkboxValue: $(this).find('td:eq(0)').text(),
                quantity: quantity,
                price: price
            });
        });
        // Send selected parts to the server for saving
        $.ajax({
            url: 'save_selected_parts.php',
            type: 'POST',
            data: { selectedParts: selectedParts },
            success: function(response) {
                // Display success message or handle the response
                console.log(response);
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error(xhr.responseText);
            }
        });
        // Close the modal after saving
        $('#exampleModal').modal('hide');
    });
</script>

</body>
</html>
