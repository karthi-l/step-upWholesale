<?php
include('../includes/session_dbConn.php');
include('../includes/bootstrap-css-js.php');

$alert_message = ""; // Variable to store the alert message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $main_brand = $_POST['main_brand'];
    $sub_brand = !empty($_POST['sub_brand']) ? $_POST['sub_brand'] : null;
    $commodity = $_POST['commodity'];
    $color = $_POST['color'];
    $stock_available = $_POST['stock_available'];
    $size_variation = $_POST['size_variation'];
    $article = $_POST['article'];
    $price = floatval($_POST['price']);
    $description = $main_brand . ' ' . ($sub_brand ? $sub_brand . ' ' : '') . $commodity . ' ' . $article . ' ' . $color;
    $image_name = $_FILES['image']['name'];
    $image_type = $_FILES['image']['type'];
    $image_data = file_get_contents($_FILES['image']['tmp_name']);
    $model_type = $_POST['model_type'];
    $material_type = $_POST['material_type'];

// Insert into footwear_models table
    $query = "INSERT INTO footwear_models (main_brand, sub_brand, commodity, article, color, price, stock_available, image_name, image_type, image_data, type, material) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "sssssdssssss", 
        $main_brand, 
        $sub_brand, 
        $commodity,  
        $article, 
        $color,
        $price,
        $stock_available, 
        $image_name, 
        $image_type, 
        $image_data,
        $model_type,
        $material_type
    );

    if ($stmt->execute()) {
        if($stock_available === "Yes"){
        $model_id = $conn->insert_id; // Get the ID of the inserted footwear model
        
        // Handle custom sizes if applicable
        if ($size_variation === "Custom-Sizes") {
            if (!empty($_POST['custom_sizes']) && !empty($_POST['custom_stocks'])) {
                $custom_sizes = $_POST['custom_sizes'];
                $custom_stocks = $_POST['custom_stocks'];

                // Validate that both arrays have the same length
                if (count($custom_sizes) === count($custom_stocks)) {
                    // Custom size insert query
                    $custom_query = "INSERT INTO footwear_stock (model_id, description, article, color, size_variation, custom_size, stock ) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $custom_stmt = $conn->prepare($custom_query);

                    // Loop through custom sizes and stocks
                    for ($i = 0; $i < count($custom_sizes); $i++) {
                        $size = trim($custom_sizes[$i]); // Trim spaces
                        $stock = intval($custom_stocks[$i]); // Convert to integer
                       
                        // Validate size and stock
                        if (!empty($size) && $stock >= 0) {
                            $custom_stmt->bind_param("issssii", $model_id, $description, $article, $color, $size_variation, $size, $stock);

                            // Debugging: log size and stock values before insertion
                            error_log("Inserting custom size: $size, stock: $stock for model_id: $model_id");

                            if (!$custom_stmt->execute()) {
                                // Log or alert if the query fails
                                error_log("Failed to insert custom size stock for model_id: $model_id");
                                $alert_message = '<div class="alert alert-danger">Failed to insert custom size stock.</div>';
                            }
                        } else {
                            $alert_message = '<div class="alert alert-danger">Invalid size or stock value for custom sizes.</div>';
                        }
                    }
                } else {
                    $alert_message = '<div class="alert alert-danger">Mismatch between custom sizes and stock counts.</div>';
                }
            } else {
                $alert_message = '<div class="alert alert-danger">Custom sizes or stocks are missing.</div>';
            }

        }else{
            $stock=$_POST['sets_available'];
            $non_custom_query = "INSERT INTO footwear_stock (model_id, description, article, color, size_variation, stock ) VALUES (?, ?, ?, ?, ?, ?)";
            $non_custom_stmt = $conn->prepare($non_custom_query);
            error_log("Failed to insert into footwear_models table");
            $alert_message = '<div class="alert alert-danger">Failed to insert footwear model.</div>';
            $non_custom_stmt->bind_param("issssi", $model_id, $description, $article, $color, $size_variation, $stock);
            if (!$non_custom_stmt->execute()) {
                // Log or alert if the query fails
                error_log("Failed to insert set stock for model_id: $model_id");
                $alert_message = '<div class="alert alert-danger">Failed to insert set size stock.</div>';
            }
        }
        

        // Set success alert message
        $alert_message = '<div class="alert alert-success">Footwear stock added/updated successfully.</div>';
        header("Location: products.php");
    } 
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Footwear Model</title>
</head>
<body>
<div class="container mt-5 mb-5">
    <h1 class="text-center mb-3 ">Upload Footwear Model </h1>
    <div class="text-center w-50 m-auto"><?php if (!empty($alert_message)) echo $alert_message; ?></div>
    
    <form method="POST" enctype="multipart/form-data" class="mt-4">
    <div class="row d-flex justify-content-center">
        <div class="mb-3 col-xs-12 col-md-6 col-lg-4 col-xl-3">
        <label for="main_brand" class="form-label">Brand</label>
            <select name="main_brand" id="main_brand" class="form-select">
                <option value="">Select Brand</option>
                <?php
                $brands_query = "SELECT DISTINCT main_brand FROM brands";
                $brands_result = $conn->query($brands_query);
                while ($brand = $brands_result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($brand['main_brand']) . "'>" . htmlspecialchars($brand['main_brand']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3 col-xs-12 col-md-6 col-lg-4 col-xl-3">
            <label for="sub_brand" class="form-label">Sub-Brand</label>
            <select name="sub_brand" id="sub_brand" class="form-select">
                <option value="">Select Sub-Brand</option>
                <?php
                    $sub_brands_query = "SELECT DISTINCT sub_brand FROM brands where sub_brand IS NOT NULL";
                    $sub_brands_result = $conn->query($sub_brands_query);
                    while ($sub_brand = $sub_brands_result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($sub_brand['sub_brand']) . "'>" . htmlspecialchars($sub_brand['sub_brand']) . "</option>";
                    }
                ?>
            </select>
        </div>
    </div>
    <div class="row d-flex justify-content-center">
        <div class="mb-3 col-xs-12 col-md-6 col-lg-4 col-xl-3">
            <label for="commodity" class="form-label">Commodity</label>
            <select name="commodity" id="commodity" class="form-select" required onchange="updateSizeOptions()">
                <option value="Select">Select Gents/Ladies/Kids</option>
                <option value="Gents">Gents</option>
                <option value="Gents-Big">Gents-Big</option>
                <option value="Ladies">Ladies</option>
                <option value="Ladies-Big">Ladies-Big</option>
                <option value="Boys">Boys</option>
                <option value="Girls">Girls</option>
                <option value="Kids">Kids</option>
                <option value="School-Shoes-Boys">School-Shoes-Boys</option>
                <option value="School-Shoes-Girls">School-Shoes-Girls</option>
            </select>
        </div>

        <div class="mb-3 col-xs-12 col-md-6 col-lg-4 col-xl-3">
            <label for="article" class="form-label">Article Number</label>
            <input type="text" name="article" id="article" class="form-control" required>
        </div>
    </div>
    <div class="row d-flex justify-content-center">
        <div class="mb-3 col-xs-12 col-md-6 col-lg-4 col-xl-3">
            <label for="model_type" class="form_label">Model-Type</label>
            <select name="model_type" id="model_type" class="form-select" required>
                <option value="">Select a model-type</option>
                <option value="V-Strap">V-Strap</option>
                <option value="T-Strap">T-Strap</option>
                <option value="X-Strap">X-Strap</option>
                <option value="Ring-Type">Ring-Type</option>
                <option value="Flip-Flop">Flip-Flop</option>
                <option value="Covered-Cup-Type">Covered-Cup-Type</option>
                <option value="Sliders">Sliders</option>
                <option value="Belt-Sandals">BeltSandals</option>
                <option value="Sports-Sandals">Sports-Sandals</option>
                <option value="Shoe-Type">Shoe-Type</option>
                <option value="Crocs-Type">Crocs-Type</option>
                <option value="Sneakers">Sneakers</option>
                <option value="Formal-Shoes">Formal-Shoes</option>
                <option value="Casual-Shoes">Causal-Shoes</option>
                <option value="Sports-Shoes">Sports-shoes</option>
                <option value="School-Shoes">School-shoes</option>
                <option value="Belly-Shoes">Belly-Shoes</option>
                <option value="Gum-Bhoots">Gum-Bhoots</option>
                <option value="Working-Shoes"></option> 
            </select>
        </div>
        <div class="mb-3 col-xs-12 col-md-6 col-lg-4 col-xl-3">
            <label for="material_type" class="form-label">Material</label>
            <select name="material_type" id="material_type" class="form-select" required>
                 <option value="">Select the Material</option>
                 <option value="PU">PU</option>
                 <option value="Rubber">Rubber</option>
                 <option value="EVA">EVA</option>
                 <option value="TPR">TPR</option>
                 <option value="Foam">Foam</option>
                 <option value="Sponge-Foam">Sponge-Foam</option>
                 <option value="Leather">Leather</option>
                 <option value="Canvas">Canvas</option>
                 <option value="Suede">Suede</option>
            </select>
        </div>
    </div>
    <div class="row d-flex justify-content-center">
        <div class="mb-3 col-xs-12 col-md-6 col-lg-4 col-xl-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" required>
        </div>

        <div class="mb-3 col-xs-12 col-md-6 col-lg-4 col-xl-3">
            <label for="color" class="form-label">Color</label>
            <input type="text" name="color" id="color" class="form-control" required>
        </div>
    </div>
    <div class="row d-flex justify-content-center">
        <div class="mb-3 col-xs-12 col-md-6 col-lg-4 col-xl-3">
            <label for="stock_available" class="form-label">Stock Available?</label>
            <select name="stock_available" id="stock_available" class="form-select" required>
                <option value="">Select Availability</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>
        <div class="mb-3 col-xs-12 col-md-6 col-lg-4 col-xl-3" id="size-variation-section">
            <label for="size_variation" class="form-label">Size Variation</label>
            <select name="size_variation" id="size_variation" class="form-select" required>
                <option value="">Select a commodity first</option>
            </select>
        </div>
        
    </div>

    <div class="row">
        <div class="mb-3 col-xs-12 col-md-6 col-lg-4 col-xl-3 m-auto" id="sets-available-section">
            <label for="sets_available" class="form-label">Number of Sets Available</label>
            <input type="number" name="sets_available" id="sets_available" class="form-control">
        </div>
        <div id="custom-sizes-section" style="display: none;">
            <h4 class="text-center">Custom Sizes</h4>
            <div id="custom-sizes-container"></div>
            <div class="d-flex justify-content-center align-items-center"><button type="button" class="btn btn-secondary mb-3 m-auto" onclick="addCustomSize()">Add Custom Size</button></div>
        </div>
    </div>
    
        <div class="col-xs-12 col-md-6 m-auto col-lg-4 col-xl-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
        </div>
        <div class="d-flex justify-content-center align-items-center mt-2"><button type="submit" class="btn btn-primary">Upload</button></div>
        
    </form>
    
</div>

</body>
</html>
