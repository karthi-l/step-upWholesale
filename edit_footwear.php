<?php
session_start();
include('db_connect.php'); // Include database connection

// Get the footwear ID from the URL
if (!isset($_GET['model_id']) || empty($_GET['model_id'])) {
    echo "<script>alert('Invalid request!'); window.location.href='fetch_footwear.php';</script>";
    exit;
}

$model_id = intval($_GET['model_id']);

// Fetch the current details of the footwear
$query = "SELECT main_brand, sub_brand, commodity, article, color, price, stock_available, image_type, image_data FROM footwear_models WHERE model_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $model_id);
$stmt->execute();
$result = $stmt->get_result();

$stock_query = "SELECT model_id, stock_id, description, article, color, size_variation, custom_size, stock FROM footwear_stock WHERE model_id = ?";
$stock_stmt = $conn->prepare($stock_query);
$stock_stmt->bind_param("i",$model_id);
$stock_stmt->execute();
$stock_result = $stock_stmt->get_result();


if ($result->num_rows === 0 and $stock_query->num_rows === 0)   {
    echo "<script>alert('Footwear model not found!'); window.location.href='fetch_footwear.php';</script>";
    exit;
}

$row = $result->fetch_assoc();
$stock_row = $stock_result->fetch_assoc();

// Handle form submission
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stock_available = $_POST['stock_available'];
    $price = floatval($_POST['price']);
    
    // Update the footwear_models table
    $update_query = "UPDATE footwear_models SET stock_available = ?, price = ? WHERE model_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sdi", $stock_available, $price, $model_id);
    $model_updated = $update_stmt->execute();

    // Check if the stock variation is Custom-Sizes
    if (isset($_POST['custom_sizes']) && isset($_POST['custom_stocks'])) {
        // Update custom sizes in footwear_stock table
        foreach ($_POST['custom_sizes'] as $stock_id => $custom_size) {
            $custom_size = intval($custom_size);
            $stock = intval($_POST['custom_stocks'][$stock_id]);

            $update_stock_query = "UPDATE footwear_stock SET custom_size = ?, stock = ? WHERE stock_id = ?";
            $update_stock_stmt = $conn->prepare($update_stock_query);
            $update_stock_stmt->bind_param("iii", $custom_size, $stock, $stock_id);
            $update_stock_stmt->execute();
        }
    } elseif (isset($_POST['sets_available']) && isset($_POST['size_variation'])) {
        // Update non-custom sizes in footwear_stock table
        $size_variation = $_POST['size_variation'];
        $sets_available = intval($_POST['sets_available']);

        $update_stock_query = "UPDATE footwear_stock SET size_variation = ?, stock = ? WHERE model_id = ?";
        $update_stock_stmt = $conn->prepare($update_stock_query);
        $update_stock_stmt->bind_param("sii", $size_variation, $sets_available, $model_id);
        $update_stock_stmt->execute();
    }

    // Redirect or show success message
    if ($model_updated) {
        echo "<script>alert('Footwear model updated successfully!'); window.location.href='fetch_footwear.php';</script>";
        exit;
    } else {
        echo "<script>alert('Failed to update the footwear model. Please try again.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Footwear Model</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5 m-auto">
    <h1>Edit Footwear Model</h1>
    <form method="POST" class="mt-4">
        <img src="data:<?php echo htmlspecialchars($row['image_type']); ?>;base64,<?php echo base64_encode($row['image_data']); ?>" alt="" class="img-fluid w-25 border rounded">
        <div class="mb-3">
            <label class="form-label"><strong>Description</strong></label>
            <p><?php echo htmlspecialchars($row['main_brand']) . ' ' . htmlspecialchars($row['sub_brand']) . ' ' . htmlspecialchars($row['commodity']) . ' ' . htmlspecialchars($row['article']); ?></p>
        </div>
        <div class="row">
        <div class="mb-3 col-xs-12 col-md-6 col-lg-4 col-xl=3 m-auto">
            <label for="stock_available" class="form-label">Stock:</label>
            <select class="form-select" name="stock_available" id="stock_available">
                <option value="<?php echo htmlspecialchars($row['stock_available']); ?>">Availability (Default) : <spam><?php echo htmlspecialchars($row['stock_available']); ?></spam></option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>
        
        <?php 
        if($row['stock_available'] === "Yes"){       
            if ($stock_row['size_variation'] !== "Custom-Sizes") { 
                echo '
                    <div class="mb-3 col-xs-12 col-md-6 col-lg-4 col-xl-3 m-auto" id="size-variation-section">
                        <label for="size_variation" class="form-label">Size Variation</label>
                        <select name="size_variation" id="size_variation" class="form-select" required>
                            <option value="' . htmlspecialchars($stock_row['size_variation']) . '">' . htmlspecialchars($stock_row['size_variation']) . '</option>
                        </select>
                    </div>';
                echo '
                    <div class="mb-3 col-xs-12 col-md-6 col-lg-4 col-xl-3 m-auto" id="sets-available-section">
                        <label for="sets_available" class="form-label">Number of Sets Available</label>
                        <input type="number" name="sets_available" id="sets_available" class="form-control" value="'. htmlspecialchars($stock_row['stock']).'">
                    </div>';
            } else {
                // For custom sizes
                echo '
                    <div id="custom-sizes-section">
                        <h4 class="text-center">Custom Sizes</h4>
                        <div id="custom-sizes-container">';
            
                // Fetch custom sizes from the database for the current model_id
                $model_id = $stock_row['model_id'];
                $custom_query = "SELECT stock_id, custom_size, stock FROM footwear_stock WHERE model_id = ? AND size_variation = 'Custom-Sizes'";
                $custom_stmt = $conn->prepare($custom_query);
                $custom_stmt->bind_param("i", $model_id);
                $custom_stmt->execute();
                $custom_result = $custom_stmt->get_result();
            
                if ($custom_result->num_rows > 0) {
                    while ($custom_row = $custom_result->fetch_assoc()) {
                        echo '
                            <div class="row custom-size-row mb- m-auto">
                                <div class="col-xs-12 col-md-6 col-lg-4 col-xl-3">
                                    <label for="custom_size_' . $custom_row['stock_id'] . '" class="form-label">Size</label>
                                    <input type="number" name="custom_sizes[' . $custom_row['stock_id'] . ']" id="custom_size_' . $custom_row['stock_id'] . '" class="form-control" value="' . htmlspecialchars($custom_row['custom_size']) . '" required>
                                </div>
                                <div class="col-xs-12 col-md-6 col-lg-4 col-xl-3 ">
                                    <label for="custom_stock_' . $custom_row['stock_id'] . '" class="form-label">Stock</label>
                                    <input type="number" name="custom_stocks[' . $custom_row['stock_id'] . ']" id="custom_stock_' . $custom_row['stock_id'] . '" class="form-control" value="' . htmlspecialchars($custom_row['stock']) . '" required>
                                </div>
                            </div>';
                    }
                } else {
                    echo '<p class="text-center">No custom sizes available.</p>';
                }
            
                echo '
                        </div>
                        <div class="d-flex justify-content-center align-items-center">
                            <button type="button" class="btn btn-secondary mb-3" onclick="addCustomSize()">Add Custom Size</button>
                        </div>
                    </div>';
            }
        } else{ 
            echo"cant update now";
        }
       ?>
       
        </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price (₹)</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control" value="<?php echo htmlspecialchars($row['price']); ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
            <a href="fetch_footwear.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
