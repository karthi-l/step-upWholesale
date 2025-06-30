<?php
include('../includes/session_dbConn.php');
if(!isset($_SESSION['user_id'])){
    include('../auth/ua-auth/user_auth.php');
    exit;
}

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
           uc.quantity,
           svn.nos_in_set
    FROM footwear_models fm
    LEFT JOIN footwear_stock fs ON fm.model_id = fs.model_id
    LEFT JOIN user_cart uc ON fm.model_id = uc.model_id 
    LEFT JOIN size_variation_nos svn ON fs.size_variation = svn.size_set
    WHERE uc.user_id = ?
    GROUP BY fm.model_id
    ORDER BY fm.main_brand, fm.sub_brand;
";


// Prepare the statement
$stmt = $conn->prepare($query);

// Bind the parameter to the statement
$stmt->bind_param("i", $user_id);   // 'i' for integer as user_id is likely an integer

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

// No need to close stmt here, it will be closed after fetching all results

$userTypeQuery = "SELECT userType FROM usersretailers WHERE user_id = ?";
$userTypeStmt = $conn->prepare($userTypeQuery);
$userTypeStmt->bind_param("i", $user_id);
$userTypeStmt->execute();
$userTypeResult = $userTypeStmt->get_result();
$userTypeRow = $userTypeResult->fetch_assoc();
$userTypeStmt->close();

$discountPercentage = 0;
if ($userTypeRow) {
    switch ($userTypeRow['userType']) {
        case 'Regular':
            $discountPercentage = 30;
            break;
        case 'Frequent':
            $discountPercentage = 32;
            break;
        case 'VIP':
            $discountPercentage = 34;
            break;
    }
}

// Initialize grand totals BEFORE the loop
$grandTotal = 0;
$grandGrossTotal = 0;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User-Cart</title>
    <?php include('../includes/inc_styles.php'); ?>
</head>
<body>
    <?php include("../includes/main_nav.php");?>
    <div class="container">
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
                $nosInSet = $row['nos_in_set'] ?? 1;
                $pieces = $row['quantity'] * $nosInSet;
                $total = $row['price'] * $pieces;
                $discountAmount = $total * ($discountPercentage / 100);
                $grossTotal = $total - $discountAmount;
                $grandTotal += $total; // Accumulate grand total
                $grandGrossTotal += $grossTotal; // Accumulate grand gross total
            ?>

        <div class="row border rounded p-2 m-2  align-items-center"
            id="footwear-model-<?php echo $row['model_id'];?>"
            data-price="<?php echo htmlspecialchars($row['price']); ?>"
            data-nos-in-set="<?php echo htmlspecialchars($nosInSet); ?>"
            data-stock-sets="<?php echo htmlspecialchars($row['stock']); ?>">
            <div class="col-3 p-2">
                <img src="data:<?php echo htmlspecialchars($row['image_type']); ?>;base64,<?php echo base64_encode($row['image_data']); ?>" 
                    alt="<?php echo htmlspecialchars($row['main_brand']); ?>" 
                    class="card-img-top rounded w-100">
            </div>
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
                    <strong>Size variation:</strong> <?php echo htmlspecialchars($row['size_variation'] == 'Custom-Sizes' ? $row['custom_size'] : $row['size_variation']); ?><br>
                    <strong>Sets Available:</strong> <?php echo htmlspecialchars($row['stock']); ?><br>
                </h4>
            </div>
            <div class="col-3 p-2 text-center" >
                <div class="d-flex align-items-center justify-content-center">
                    <button class="btn btn-sm btn-outline-secondary quantity-btn" onclick="updateQuantity(this, 'decrease', <?php echo $row['model_id']; ?>)">-</button>
                    <input type="text" class="form-control text-center mx-2 quantity-input" id="qty-<?php echo $row['model_id']; ?>" value="<?php echo htmlspecialchars($row['quantity']); ?>" readonly style="width: 50px;">
                    <button class="btn btn-sm btn-outline-secondary quantity-btn" onclick="updateQuantity(this, 'increase', <?php echo $row['model_id']; ?>)">+</button>
                </div>
                <strong>Nos in Set:</strong> <?php echo htmlspecialchars($nosInSet); ?><br>
                <strong>Total Pieces:</strong> <span id="pieces-<?php echo $row['model_id']; ?>"><?php echo $pieces; ?></span><br>
                <strong>Total: ₹<span id="total-<?php echo $row['model_id']; ?>"><?php echo number_format($total, 2); ?></span></strong><br>
                <strong>Gross Total: ₹<span id="gross-total-<?php echo $row['model_id']; ?>"><?php echo number_format($grossTotal, 2); ?></span></strong>
                <br>
                <div data-model-id="<?php echo $row['model_id']; ?>">
                    <button class="btn btn-sm btn-danger mt-2 remove-from-cart" data-model-id="<?php echo $row['model_id']; ?>" >Remove</button>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        <?php $stmt->close(); // Close the statement here after fetching all results ?>

        <div class="mt-4 p-2 m-2 text-end border rounded d-flex flex-row justify-content-center align-items-center ">
            <strong class="mx-2">Grand Total: ₹<span id="grand-total"><?php echo number_format($grandTotal, 2); ?></span></strong>
            <strong class="mx-2">Grand Gross Total: ₹<span id="grand-gross-total"><?php echo number_format($grandGrossTotal, 2); ?></span></strong>
            <button class="btn btn-primary mx-2"><a href="bill-view.php" class="text-white" style="text-decoration:none;">Place Order</a></button>
        </div>
    </div>
    <?php include('../includes/inc_scripts.php');?>
<script>
    const discountPercentage = <?php echo $discountPercentage; ?>;
    function calculateGrossTotal(price, sets, nosInSet) {
        const totalPieces = sets * nosInSet;
        const total = price * totalPieces;
        const discount = total * (discountPercentage / 100);
        return total - discount;
    }
    function updateQuantity(button, action, model_id) {
        let rowEl = document.getElementById('footwear-model-' + model_id);
        let qtyInput = document.getElementById('qty-' + model_id);
        let totalSpan = document.getElementById('total-' + model_id);
        let grossTotalSpan = document.getElementById('gross-total-' + model_id);

        let price = parseFloat(rowEl.getAttribute('data-price'));
        let nosInSet = parseInt(rowEl.getAttribute('data-nos-in-set')) || 1;
        let availableSets = parseInt(rowEl.getAttribute('data-stock-sets')) || 0;

        let quantity = parseInt(qtyInput.value);

        if (action === 'increase') {
            if (quantity < availableSets) {
                quantity++;
            } else {
                alert("Maximum stock limit reached.");
                return;
            }
        } else if (action === 'decrease' && quantity > 1) {
            quantity--;
        }

        qtyInput.value = quantity;
        let totalPieces = quantity * nosInSet;
        document.getElementById("pieces-" + model_id).innerText = totalPieces; // Update total pieces here
        totalSpan.innerText = (price * totalPieces).toFixed(2);
        grossTotalSpan.innerText = calculateGrossTotal(price, quantity, nosInSet).toFixed(2);


        $.ajax({
            url: "update_cart_quantity.php",
            type: "POST",
            data: { model_id: model_id, quantity: quantity },
            success: function(response) {
                console.log("Quantity updated:", response);
                updateGrandTotals(); // Call this on success
            },
            error: function(xhr, status, error) {
                console.error("Error updating quantity:", error);
            }
        });

        // updateGrandTotals(); // Moved inside AJAX success to ensure consistent calculations
    }
    function updateGrandTotals() {
        let grandTotal = 0;
        let grandGrossTotal = 0;

        $(".row.border.rounded").each(function () {
            const modelId = $(this).attr("id").split('-').pop();
            const price = parseFloat($(this).data("price")) || 0;
            const nosInSet = parseInt($(this).data("nos-in-set")) || 1;
            const quantity = parseInt($("#qty-" + modelId).val()) || 0;

            const totalPieces = quantity * nosInSet;
            const total = price * totalPieces;
            const discount = total * (discountPercentage / 100);
            const grossTotal = total - discount;

            grandTotal += total;
            grandGrossTotal += grossTotal;
            // No need to update individual 'pieces' span here, it's done in updateQuantity
        });

        $("#grand-total").text(grandTotal.toFixed(2));
        $("#grand-gross-total").text(grandGrossTotal.toFixed(2));
    }
    $(document).ready(function() {
        // Initial calculation for individual item totals (this is fine)
        $(".row.border.rounded").each(function() {
            const modelId = $(this).attr("id").split('-').pop();
            const price = parseFloat($(this).data("price"));
            const quantity = parseInt($("#qty-" + modelId).val());
            const nosInSet = parseInt($(this).data("nos-in-set")) || 1;

            const totalPieces = quantity * nosInSet;
            const total = price * totalPieces;
            const grossTotal = calculateGrossTotal(price, quantity, nosInSet);

            $("#total-" + modelId).text(total.toFixed(2));
            $("#gross-total-" + modelId).text(grossTotal.toFixed(2));
        });
        updateGrandTotals(); // Ensure this is called on page load

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
                        $("#footwear-model-" + model_id).remove(); 
                        updateGrandTotals(); // Update grand totals after removal
                        // Check if the cart is now empty and redirect
                        if ($(".row.border.rounded").length === 0) {
                            window.location.reload(); // Reload to show "Cart is Empty" message
                        }
                    } else {
                        alert("Failed to remove from cart! Try again. Server message: " + (response ? response.message : "No message."));
                    }
                },
                error: function(xhr, status, error) {
                    console.log("AJAX error:", status, error);
                    console.log("Response Text:", xhr.responseText); // Log the raw response
                    alert("An error occurred while trying to remove the item.");
                }
            });
            // Do NOT call updateGrandTotals() here, it should be in success callback
        });
    });
</script>
</body>
</html>