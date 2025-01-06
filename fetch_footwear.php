<?php
session_start();
include('db_connect.php');

// Fetch footwear models
$query = "SELECT model_id, main_brand, sub_brand, commodity, article, color,  price, stock_available, image_type, image_data, type, material FROM footwear_models ORDER BY main_brand, sub_brand";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footwear Models</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card img {
            width:100%;
            max-height: 250px;
            object-fit: contain;
            border-radius: 0.33rem;
        }
        .card-body {
            overflow:auto;
            max-height: 210px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Footwear Models</h1>
        <div class="d-flex align-items-center">
        <input type="text" class=" form-control mx-2" placeholder="search model">
        <a href="upload_footwear.php" class="btn btn-primary w-25">Add New Model</a>
        </div>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <div class="row g-4">
        <?php while ($row = $result->fetch_assoc()): ?>
    <div class="col-6 col-sm-6 col-md-4 col-lg-3">
        <div class="card shadow-sm">
            <img src="data:<?php echo htmlspecialchars($row['image_type']); ?>;base64,<?php echo base64_encode($row['image_data']); ?>" alt="<?php echo htmlspecialchars($row['main_brand']); ?>" class="card-img-top">
            <div class="card-body">
                <h6 class="card-title" style="font-weight:700;!important">
                    <?php 
                    echo htmlspecialchars($row['main_brand']) . ' ' . 
                         (!empty($row['sub_brand']) ? htmlspecialchars($row['sub_brand']) . ' ' : '') . 
                         htmlspecialchars($row['commodity']) . ' ' . 
                         htmlspecialchars($row['article']); 
                    ?>
                </h6>
                <p class="card-text border rounded">
                    <strong>Type: </strong><?php echo htmlspecialchars($row['type']); ?><br>
                    <strong>Material: </strong><?php echo htmlspecialchars($row['material']); ?><br>
                    <strong>Color: </strong> <?php echo htmlspecialchars($row['color']); ?><br>
                    <strong>Price: </strong>₹<?php echo htmlspecialchars($row['price']); ?><br>
                    <strong>Size-Variation: </strong> 
                    
                    <?php
    if (!empty($row['stock_available']) && $row['stock_available'] === "Yes") {
        // Fetch all size variations and stocks
        $model_id = $row['model_id'];
        $size_query = "SELECT size_variation, custom_size, stock FROM footwear_stock WHERE model_id = ?";
        $size_stmt = $conn->prepare($size_query);
        $size_stmt->bind_param("i", $model_id);
        $size_stmt->execute();
        $size_result = $size_stmt->get_result();

        // To store custom sizes
        $custom_sizes = [];

        // Check if we have any rows for non-custom sizes
        $has_non_custom_sizes = false;

        if ($size_result->num_rows > 0) {
            while ($size_row = $size_result->fetch_assoc()) {
                if ($size_row['size_variation'] === "Custom-Sizes") {
                    // Collect custom sizes
                    $custom_sizes[] = [
                        'size' => $size_row['custom_size'],
                        'stock' => $size_row['stock']
                    ];
                } else {
                    // Show non-custom sizes directly
                    $has_non_custom_sizes = true;
                    echo htmlspecialchars($size_row['size_variation']) . "<br>";
                    echo "<strong>Stock:</strong> " . htmlspecialchars($size_row['stock']);
                }
            }
        } else {
            echo "Stock availability: No";
        }

        // Display custom sizes separately only once after the loop
        if (!empty($custom_sizes)) {
            echo "Custom Sizes<br>";
            foreach ($custom_sizes as $custom_size) {
                echo "Size: " . htmlspecialchars($custom_size['size']) . " - Stock: " . htmlspecialchars($custom_size['stock']) . "<br>";
            }
        }

        // If no non-custom sizes and no custom sizes, display message
        if (!$has_non_custom_sizes && empty($custom_sizes)) {
            echo "No stock available.";
        }

    } else {
        echo "Stock availability: No";
    }
    ?>
                    
                    
                </p>
            </div>
            <div class="card-footer d-flex flex-column flex-xxl-row justify-content-center">
                <a href="edit_footwear.php?model_id=<?php echo $row['model_id']; ?>" class="btn btn-sm btn-success mb-1 mb-xxl-0 mx-sm-2">Change Stock/Price</a>
                <a href="remove_footwear.php?model_id=<?php echo $row['model_id']; ?>" class="btn btn-sm btn-danger mx-sm-2" onclick="return confirm('Are you sure you want to remove this model?');">Remove</a>
            </div>
        </div>  
    </div>
<?php endwhile; ?>

        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <p>No footwear models available. Add new models to display them here.</p>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
