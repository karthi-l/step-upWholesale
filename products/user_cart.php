<?php
include('../includes/session_dbConn.php');
include('../includes/bootstrap-css-js.php');
include('../auth/ua-auth/user_auth.php');

// Assuming the user is logged in and session is active
$user_id = $_SESSION['user_id'];

// SQL Query - using '?' placeholder for prepared statements
$query = "
    SELECT fm.model_id, 
           fm.main_brand, 
           fm.sub_brand, 
           fm.commodity, 
           fm.article, 
           fm.color, 
           fm.price, 
           fm.stock_available, 
           fm.image_type, 
           fm.image_data, 
           fm.type, 
           fm.material, 
           GROUP_CONCAT(DISTINCT fs.size_variation ORDER BY fs.size_variation) AS size_variation,
           GROUP_CONCAT(DISTINCT fs.custom_size ORDER BY fs.custom_size) AS custom_size,
           GROUP_CONCAT(fs.stock) AS stock,
           uc.model_id AS cart_model_id, 
           uc.user_id, 
           uc.quantity 
    FROM footwear_models fm
    LEFT JOIN footwear_stock fs ON fm.model_id = fs.model_id
    LEFT JOIN user_cart uc ON fm.model_id = uc.model_id 
    WHERE uc.user_id = ?  -- Use '?' placeholder for MySQLi prepared statement
    GROUP BY fm.model_id
    ORDER BY fm.main_brand, fm.sub_brand;
";

// Prepare the statement
$stmt = $conn->prepare($query);

// Bind the parameter to the statement
$stmt->bind_param("i", $user_id);  // 'i' for integer as user_id is likely an integer

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Close the prepared statement
if($result->num_rows == 0 ){
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Empty Cart - Wholesale Footwear Management</title>
    </head>
    <body>
        <div class='container mt-5 row m-auto'>
            <div class='alert alert-danger text-center col-12 col-md-9 col-lg-8 col-xl-6 col-xxl-5 m-auto'>
                <h4>Your Cart is Empty.</h4>
                    <p>Add Products to Cart to show here</p>
                    <div class='d-flex justify-content-center'>
                        <a href='products.php' class='btn btn-primary mx-2'>Products</a>
                        <a href='../index.php' class='btn btn-info mx-2'>Home</a>
                    </div>
            </div>
        </div>
    </body>
    </html>
    ";
    exit();              
}

$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User-Cart</title>
</head>
<body>
    <?php include("../includes/main_nav.php");?>
    <div class="container">
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="row border rounded p-2  align-items-center" id="footwear-model-<?php echo $row['model_id'];?>" data-price="<?php echo htmlspecialchars($row['price']); ?>">
            <!-- Product Image -->
            <div class="col-3 p-2">
                <img src="data:<?php echo htmlspecialchars($row['image_type']); ?>;base64,<?php echo base64_encode($row['image_data']); ?>" 
                    alt="<?php echo htmlspecialchars($row['main_brand']); ?>" 
                    class="card-img-top rounded w-100">
            </div>

            <!-- Product Details -->
            <div class="col-6 p-2">
                <h2 class="card-title">
                    <?php 
                    echo '<strong>'. htmlspecialchars($row['main_brand']) . ' ' . 
                        (!empty($row['sub_brand']) ? htmlspecialchars($row['sub_brand']) . ' ' : '') . 
                        htmlspecialchars($row['commodity']) . ' ' . htmlspecialchars($row['material']) .' ' . 
                        htmlspecialchars($row['article']).' '. htmlspecialchars($row['color']).'</strong>'; 
                    ?>
                </h2>
                <h4 class="card-text mt-3">
                    <strong>Type:</strong> <?php echo htmlspecialchars($row['type']); ?><br>
                    <strong>Price:</strong> ₹<?php echo htmlspecialchars($row['price']); ?><br>
                    <strong>Size:</strong> <?php echo htmlspecialchars($row['size_variation'] == 'Custom-Sizes' ? $row['custom_size'] : $row['size_variation']); ?><br>
                    <strong>Stock:</strong> <?php echo htmlspecialchars($row['stock']); ?><br>
                </h4>
            </div>

            <!-- Quantity Control & Remove Button -->
            <div class="col-3 p-2 text-center" >
                <div class="d-flex align-items-center justify-content-center">
                    <button class="btn btn-sm btn-outline-secondary quantity-btn" onclick="updateQuantity(this, 'decrease', <?php echo $row['model_id']; ?>)">-</button>
                    <input type="text" class="form-control text-center mx-2 quantity-input" id="qty-<?php echo $row['model_id']; ?>" value="<?php echo htmlspecialchars($row['quantity']); ?>" readonly style="width: 50px;">
                    <button class="btn btn-sm btn-outline-secondary quantity-btn" onclick="updateQuantity(this, 'increase', <?php echo $row['model_id']; ?>)">+</button>
                </div>
                <strong>Total: ₹<span id="total-<?php echo $row['model_id']; ?>"><?php echo htmlspecialchars($row['price'] * $row['quantity']); ?></span></strong>
                <br>
                <div data-model-id="<?php echo $row['model_id'];?>">
                    <button class="btn btn-sm btn-danger mt-2 remove-from-cart" data-model-id="<?php echo $row['model_id']; ?>" >Remove</button>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
    <script>
    function updateQuantity(button, action, model_id) {
        let qtyInput = document.getElementById('qty-' + model_id);
        let totalSpan = document.getElementById('total-' + model_id);

        // Get the price from the data-price attribute of the parent row
        let price = parseFloat(document.getElementById('footwear-model-' + model_id).getAttribute('data-price'));

        let quantity = parseInt(qtyInput.value);
        if (action === 'increase') {
            quantity++;
        } else if (action === 'decrease' && quantity > 1) {
            quantity--;
        }

        qtyInput.value = quantity;
        totalSpan.innerText = (price * quantity).toFixed(2);

        // You should also make an AJAX call here to update the quantity in the database/session
        $.ajax({
            url: "update_cart_quantity.php", // Create this new PHP file
            type: "POST",
            data: { model_id: model_id, quantity: quantity },
            success: function(response) {
                console.log("Quantity updated:", response);
                // Optionally handle success messages
            },
            error: function(xhr, status, error) {
                console.error("Error updating quantity:", error);
                // Optionally handle error messages (e.g., revert quantity)
            }
        });
    }


    $(document).ready(function() {
        $(".remove-from-cart").click(function() {
            var model_id = $(this).data("model-id");
            var user_id = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

            if (user_id === null) {
                alert("You need to log in to remove items from the cart.");
                return;
            }

            console.log("Removing item:", { model_id: model_id, user_id: user_id });

            $.ajax({
                url: "removefrom_cart.php",
                type: "POST",
                dataType: "json", // Expect JSON response
                data: { model_id: model_id, user_id: user_id },
                success: function(response) {
                    console.log("Server response:", response);
                    if (response && response.status === "success") {
                        console.log("Successfully removed item:", model_id);
                        $("#footwear-model-" + model_id).hide(); // Use .hide() for display: none
                    } else {
                        alert("Failed to remove from cart! Try again.");
                    }
                },
                error: function(xhr, status, error) {
                    console.log("AJAX error:", status, error);
                    console.log("Response Text:", xhr.responseText); // Log the raw response
                }
            });
        });
    });
</script>
    </div>
</body>
</html>
