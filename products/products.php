<?php
include('../includes/session_dbConn.php');
include('../includes/filter_query.php');
include('../includes/bootstrap-css-js.php');

if (isset($_SESSION['success_message'])) {
    echo '<script>alert("' . htmlspecialchars($_SESSION['success_message']) . '")</script>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<script>alert("' . htmlspecialchars($_SESSION['error_message']) . '")</script>';
    unset($_SESSION['error_message']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footwear Models</title>
    <style>
        .footwear_list .card {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .footwear_list .card img {
            width:100%;
            max-height: 250px;  
            object-fit: contain;
            border-radius: 0.33rem;
        }
        .footwear_list .card-body .card-text {
            overflow:auto;
            max-height: 100px;
            text-align: center;
        }
        .sticky_filter{
            position: sticky;
            top:9%;
            height:100%;
        }
        .dropdown-menu {
            width: 100%; /* Adjust width to fit your form */
            height: 100%; /* Optional: Limit the height */
        
        }

    </style>
</head>
<body>
    <?php include('../includes/footwear_fetching.php'); ?>
    <script>
        $(document).ready(function() {
            $(".add-to-cart").click(function() {
                var model_id = $(this).data("model-id");
                var quantity = $("#quantity_" + model_id).val();
                var user_id = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

                if (user_id === null) {
                    alert("You need to log in to add items to the cart.");
                    return;
                }

                console.log("Sending data:", { model_id: model_id, quantity: quantity, user_id: user_id });

                $.ajax({
                    url: "addto_cart.php",
                    type: "POST",
                    data: { model_id: model_id, quantity: quantity, user_id: user_id },
                    success: function(response) {
                        console.log("Server response:", response);
                        if (response.trim() === "success") {
                            $("#cart-status-" + model_id).css("display","block");
                            $("#cart-status-" + model_id).html("✔️");
                            $("#add-to-cart-" + model_id).css("display","none");
                            $("#remove-from-cart-" + model_id).css("display","block");
                        } else {
                            alert("Failed to add to cart! Try again.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX error:", status, error);
                    }
                });
            });
        });
        $(document).ready(function() {
    var user_id = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

    if (user_id !== null) {
        // Fetch cart data for logged-in user
        $.ajax({
            url: "check_cart.php",  // PHP file to check cart items
            type: "POST",
            data: { user_id: user_id },
            success: function(response) {
                console.log("Cart items:", response);
                var cartItems = JSON.parse(response); // Convert response to JSON object

                // Loop through cart items and update UI
                cartItems.forEach(function(item) {
                    $("#cart-status-" + item.model_id).html("✔️");
                    $("#add-to-cart-"+ item.model_id).css("display","none");
                    $("#remove-from-cart-"+ item.model_id).css("display","block");
                });
            },
            error: function(xhr, status, error) {
                console.log("AJAX error:", status, error);
            }
        });
    }
});
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
                data: { model_id: model_id, user_id: user_id },
                success: function(response) {
                    console.log("Server response:", response);
                    if (response.status === "success") {
                        $("#cart-status-" + model_id).css("display","none");
                        $("#remove-from-cart-" + model_id).css("display","none");
                        $("#add-to-cart-" + model_id).css("display","block");

                    } else {
                        alert("Failed to remove from cart! Try again.");
                    }
                },
                error: function(xhr, status, error) {
                    console.log("AJAX error:", status, error);
                }
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
    const removeButtons = document.querySelectorAll('.remove-btn');

    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modelId = this.getAttribute('data-model-id');
            const productCardId = `product-card-${modelId}`;
            const productCard = document.getElementById(productCardId);

            if (confirm('Are you sure you want to remove this model?')) {
                fetch('remove_footwear.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'model_id=' + encodeURIComponent(modelId)
                })
                .then(response => response.json())
                .then(data => {
                    console.log('AJAX Response:', data); // Check the entire response
                    if (data.success) {
                        console.log('Removing card with ID:', productCardId);
                        if (productCard) {
                            productCard.remove();
                            console.log('Card removed successfully from DOM.');
                            // Optionally display a success message
                        } else {
                            console.error('Product card not found:', productCardId);
                        }
                    } else {
                        console.error('Error removing model:', data.message);
                        // Optionally display an error message to the user
                    }
                })
                .catch(error => {
                    console.error('There was an error with the AJAX request:', error);
                    // Optionally display a generic error message to the user
                });
            }
        });
    });
});
    </script>
</body>
</html>
