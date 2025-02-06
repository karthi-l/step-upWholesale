<?php

include('session_dbConn.php');
if(isset($_SESSION['admin_id'])){
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Account Center - Wholesale Footwear Management</title>
        <!-- Bootstrap 5 CSS -->
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head> 
    <body>
        <div class='container row mt-5 m-auto'>
            <div class='alert alert-danger text-center col-12 col-md-9 col-lg-8   m-auto'>
                <h2>With Great Powers Comes Grate Responsibilities.</h2>
                <h5>You must be logged in as a Admin to access.</h5>
                <div class='d-flex justify-content-center mt-3'>
                <a href='admin_login.php' class='btn btn-primary mx-2'>Login</a>
                <a href='index.php' class='btn btn-info mx-2'>Home</a>
                <a href='user_dashboard.php' class='btn btn-primary mx-2'>User-Portal</a>
                </div>
            </div>
        </div>
        <!-- Bootstrap 5 JS -->
        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'></script>
    </body>
    </html>";
    exit;
}

if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success text-center">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger text-center">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}

// Build dynamic query based on filters
$whereClauses = [];

if(isset($_GET['search']) && !empty($_GET['search'])){
    $searcharray = $conn->real_escape_string($_GET['search']) ;
    $array = explode(' ', $searcharray); // Split by a single space
    foreach($array as $search){
    $whereClauses[] =  "(
        fm.main_brand LIKE '%".$search."%' OR
        fm.sub_brand LIKE '%".$search."%' OR
        fm.commodity LIKE '%".$search."%' OR
        fm.article LIKE '%".$search."%' OR
        fm.color LIKE '%".$search."%' OR
        fm.price LIKE '%".$search."%' OR
        fm.stock_available LIKE '%".$search."%' OR
        fm.type LIKE '%".$search."%' OR
        fm.material LIKE '%".$search."%' OR
        fs.size_variation LIKE '%".$search."%' OR
        fs.custom_size LIKE '%".$search."%') 
        ";
    }
}
if (isset($_GET['main_brand']) && !empty($_GET['main_brand'])) {
    $whereClauses[] = "fm.main_brand = '" . $conn->real_escape_string($_GET['main_brand']) . "'";
}
if (isset($_GET['sub_brand']) && !empty($_GET['sub_brand'])) {
    $whereClauses[] = "fm.sub_brand = '" . $conn->real_escape_string($_GET['sub_brand']) . "'";
}
if (isset($_GET['color']) && !empty($_GET['color'])) {
    $whereClauses[] = "fm.color = '" . $conn->real_escape_string($_GET['color']) . "'";
}
if (isset($_GET['commodity']) && !empty($_GET['commodity'])) {
    $whereClauses[] = "fm.commodity = '" . $conn->real_escape_string($_GET['commodity']) . "'";
}
if (isset($_GET['stock_available']) && !empty($_GET['stock_available'])) {
    // We join with footwear_stock table to filter by stock availability
    $whereClauses[] = "fm.stock_available = '" . $conn->real_escape_string($_GET['stock_available']) . "'";
}
if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
    $whereClauses[] = "fm.price >= " . (int)$_GET['min_price'];
}
if (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
    $whereClauses[] = "fm.price <= " . (int)$_GET['max_price'];
}
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $whereClauses[] = "fm.type = '" . $conn->real_escape_string($_GET['type']) . "'";
}
if (isset($_GET['material']) && !empty($_GET['material'])) {
    $whereClauses[] = "fm.material = '" . $conn->real_escape_string($_GET['material']) . "'";
}
if (isset($_GET['size_variation']) && !empty($_GET['size_variation'])) {
    $whereClauses[] = "fs.size_variation = '" . $conn->real_escape_string($_GET['size_variation']) . "'";
}



// Combine all filter conditions
$whereSql = '';
if (count($whereClauses) > 0) {
        $whereSql = " WHERE " . implode(' AND ', $whereClauses);

}

// Modify the query to include a JOIN with footwear_stock

$query = "
    SELECT fm.model_id, fm.main_brand, fm.sub_brand, fm.commodity, fm.article, fm.color, fm.price, fm.stock_available, fm.image_type, fm.image_data, fm.type, fm.material, 
           GROUP_CONCAT(DISTINCT fs.size_variation ORDER BY fs.size_variation) AS size_variation,
           GROUP_CONCAT(DISTINCT fs.custom_size ORDER BY fs.custom_size) AS custom_size,
           GROUP_CONCAT(fs.stock) AS stock
    FROM footwear_models fm
    LEFT JOIN footwear_stock fs ON fm.model_id = fs.model_id
    " . $whereSql . " 
    GROUP BY fm.model_id
    ORDER BY fm.main_brand, fm.sub_brand
";

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
            top: 5%;
            height:100%;

        }
        .dropdown-menu {
            width: 100%; /* Adjust width to fit your form */
            height: 100%; /* Optional: Limit the height */
        
        }

    </style>
    <link rel="stylesheet" href="app.css">
</head>
<body>
<nav class="navbar navbar-dark fixed-top navbar-expand-lg bg-dark shadow-sm ">
                <div class="container-fluid d-flex align-items-center d-flex align-items-center flex-row ">
                    <a href="index.php" class="navbar-brand d-none d-md-block">
                        <img src="img/st-logo.png" alt="st-logo" id="nav-logo" class="img-fluid rounded d-inline-block border border-white" >
                    </a>
                    <div class="dropdown-show d-md-none">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Filter
                        </button>
                        <div class="dropdown-menu mt-2">
                                <form method="GET" action="footwear_stock.php" class="dropdown-item bg-light">
                                    <h1 class="text-center bg-info w-50 m-auto mb-2 border rounded">Filter</h1>
                                    <div class="mb-1 ">
                                            <input type="text" name="search" id="search" class="form-control" placeholder="Search-Here">
                                    </div>
                                    <!-- Brand Filter -->
                                    <div class="mb-1">
                                        <label for="main_brand" class="form-label">Brand</label>
                                        <select name="main_brand" id="main_brand" class="form-select">
                                            <option value="">Select Brand</option>
                                            <?php
                                            $brands_query = "SELECT DISTINCT main_brand FROM footwear_models";
                                            $brands_result = $conn->query($brands_query);
                                            while ($brand = $brands_result->fetch_assoc()) {
                                                echo "<option value='" . htmlspecialchars($brand['main_brand']) . "'>" . htmlspecialchars($brand['main_brand']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <!-- Brand Filter -->
                                    <div class="mb-1">
                                        <label for="sub_brand" class="form-label">Sub-Brand</label>
                                        <select name="sub_brand" id="sub_brand" class="form-select">
                                            <option value="">Select Sub-Brand</option>
                                            <?php
                                            $brands_query = "SELECT DISTINCT sub_brand FROM footwear_models WHERE sub_brand IS NOT NULL";
                                            $brands_result = $conn->query($brands_query);
                                            while ($brand = $brands_result->fetch_assoc()) {
                                                echo "<option value='" . htmlspecialchars($brand['sub_brand']) . "'>" . htmlspecialchars($brand['sub_brand']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <!-- Commodity Filter -->
                                    <div class="mb-1">
                                        <label for="commodity" class="form-label">Commodity</label>
                                        <select name="commodity" id="commodity" class="form-select">
                                            <option value="">Select Commodity</option>
                                            <?php
                                            // Fetch distinct commodities from the database
                                            $commodity_query = "SELECT DISTINCT commodity FROM footwear_models";
                                            $commodity_result = $conn->query($commodity_query);
                                            while ($commodity = $commodity_result->fetch_assoc()) {
                                                echo "<option value='" . htmlspecialchars($commodity['commodity']) . "'>" . htmlspecialchars($commodity['commodity']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <!-- Color Filter -->
                                    <div class="mb-1">
                                        <label for="color" class="form-label">Color</label>
                                        <select name="color" id="color" class="form-select">
                                            <option value="">Select Color</option>
                                            <?php
                                            // Fetch distinct colors from the database
                                            $colors_query = "SELECT DISTINCT color FROM footwear_models";
                                            $colors_result = $conn->query($colors_query);
                                            while ($color = $colors_result->fetch_assoc()) {
                                                echo "<option value='" . htmlspecialchars($color['color']) . "'>" . htmlspecialchars($color['color']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <!-- Type Filter -->
                                    <div class="mb-1">
                                        <label for="type" class="form-label">Color</label>
                                        <select name="type" id="type" class="form-select">
                                            <option value="">Select Type</option>
                                            <?php
                                            // Fetch distinct colors from the database
                                            $type_query = "SELECT DISTINCT type FROM footwear_models";
                                            $type_result = $conn->query($type_query);
                                            while ($type = $type_result->fetch_assoc()) {
                                                echo "<option value='" . htmlspecialchars($type['type']) . "'>" . htmlspecialchars($type['type']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <!-- Material Filter -->
                                    <div class="mb-1">
                                        <label for="material" class="form-label">Color</label>
                                        <select name="material" id="material" class="form-select">
                                            <option value="">Select Material</option>
                                            <?php
                                            // Fetch distinct colors from the database
                                            $type_query = "SELECT DISTINCT material FROM footwear_models";
                                            $type_result = $conn->query($type_query);
                                            while ($material = $type_result->fetch_assoc()) {
                                                echo "<option value='" . htmlspecialchars($material['material']) . "'>" . htmlspecialchars($material['material']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <!-- Size Variation Filter -->
                                    <div class="mb-1">
                                        <label for="size_variation" class="form-label">Size Variation</label>
                                        <select name="size_variation" id="size_variation" class="form-select">
                                            <option value="">Select Size Variation</option>
                                            <?php
                                            $size_variation_query = "SELECT DISTINCT size_variation FROM footwear_stock";
                                            $size_variation_result = $conn->query($size_variation_query);
                                            while ($size_variation = $size_variation_result->fetch_assoc()) {
                                                echo "<option value='" . htmlspecialchars($size_variation['size_variation']) . "'>" . htmlspecialchars($size_variation['size_variation']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Stock Availability Filter -->
                                    <div class="mb-1">
                                        <label for="stock_available" class="form-label">Stock Availability</label>
                                        <select name="stock_available" id="stock_available" class="form-select">
                                            <option value="">Select Stock Availability</option>
                                            <option value="Yes">In Stock</option>
                                            <option value="No">Out of Stock</option>
                                        </select>
                                    </div>
                                    <!-- Price Filter -->
                                    <div class="mb-1">
                                        <label for="price" class="form-label">Price</label>
                                        <div class="row">
                                            <input type="number" name="min_price" class="form-control col ms-3 me-1 ps-1 text-center" placeholder="Min Price" min="0">
                                            <input type="number" name="max_price" class="form-control col me-3 ms-1 ps-1 text-center" placeholder="Max Price" min="0">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary mt-1" id="submit_button" name="submit_buttons">Apply Filters</button>
                                    </div>
                                </form>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center">
                        
                        <a href="user_dashboard.php" class="d-block d-lg-none border rounded border-white">
                            <img src="img/account-logo.png" alt="Account" class="img-fluid hov-nav nav-align" width=39px>
                        </a>
                        <button class="navbar-toggler mx-1 mx-xs-0 border border-white hov-nav" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>         
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item  mx-1 border border-white rounded my-1 my-lg-0"><a href="about.php" class="nav-link active text-light hov-nav nav-align">About-Us</a></li>
                            <li class="nav-item  mx-1 border border-white rounded my-1 my-lg-0"><a href="products.php" class="nav-link active text-light hov-nav nav-align">Products</a></li>
                            <li class="nav-item  mx-1 border border-white rounded my-1 my-lg-0"><a href="catalogue.php" class="nav-link active text-light hov-nav nav-align">Catalogue</a></li>
                            <li class="nav-item mx-1 border border-white rounded d-none d-lg-block"><a href="user_dashboard.php" class="nav-link active hov-nav nav-align"><img src="img/account-logo.png" alt="Account" class="img-fluid" width=20px></a></li>    
                        </ul>
                    </div>
                </div>
            </nav>    
    <div class="container-fluid mt-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h1 class="">Footwear-Stock</h1>
            <div class="d-flex align-items-center justify-content-center">
                <a href="upload_footwear.php" class="btn btn-primary mx-1" style="width:120%">Add New Model</a>
                <a href="index.php" class="btn btn-primary w-50 mx-1">Home</a>
            </div>
        </div>
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="d-none d-md-block col-md-4 col-xl-3 col-xxl-2 sticky_filter">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center border rounded bg-info text-white py-1">Filter</h5>
                        <form method="GET" action="footwear_stock.php">
                            <div class="mb-1 ">
                                    <input type="text" name="search" id="search" class="form-control" placeholder="Search-Here">
                            </div>
                            <!-- Brand Filter -->
                            <div class="mb-1">
                                <label for="main_brand" class="form-label">Brand</label>
                                <select name="main_brand" id="main_brand" class="form-select">
                                    <option value="">Select Brand</option>
                                    <?php
                                    $brands_query = "SELECT DISTINCT main_brand FROM footwear_models";
                                    $brands_result = $conn->query($brands_query);
                                    while ($brand = $brands_result->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($brand['main_brand']) . "'>" . htmlspecialchars($brand['main_brand']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- Brand Filter -->
                            <div class="mb-1">
                                <label for="sub_brand" class="form-label">Sub-Brand</label>
                                <select name="sub_brand" id="sub_brand" class="form-select">
                                    <option value="">Select Sub-Brand</option>
                                    <?php
                                    $brands_query = "SELECT DISTINCT sub_brand FROM footwear_models WHERE sub_brand IS NOT NULL";
                                    $brands_result = $conn->query($brands_query);
                                    while ($brand = $brands_result->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($brand['sub_brand']) . "'>" . htmlspecialchars($brand['sub_brand']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- Commodity Filter -->
                            <div class="mb-1">
                                <label for="commodity" class="form-label">Commodity</label>
                                <select name="commodity" id="commodity" class="form-select">
                                    <option value="">Select Commodity</option>
                                    <?php
                                    // Fetch distinct commodities from the database
                                    $commodity_query = "SELECT DISTINCT commodity FROM footwear_models";
                                    $commodity_result = $conn->query($commodity_query);
                                    while ($commodity = $commodity_result->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($commodity['commodity']) . "'>" . htmlspecialchars($commodity['commodity']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- Color Filter -->
                            <div class="mb-1">
                                <label for="color" class="form-label">Color</label>
                                <select name="color" id="color" class="form-select">
                                    <option value="">Select Color</option>
                                    <?php
                                    // Fetch distinct colors from the database
                                    $colors_query = "SELECT DISTINCT color FROM footwear_models";
                                    $colors_result = $conn->query($colors_query);
                                    while ($color = $colors_result->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($color['color']) . "'>" . htmlspecialchars($color['color']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- Type Filter -->
                            <div class="mb-1">
                                <label for="type" class="form-label">Color</label>
                                <select name="type" id="type" class="form-select">
                                    <option value="">Select Type</option>
                                    <?php
                                    // Fetch distinct colors from the database
                                    $type_query = "SELECT DISTINCT type FROM footwear_models";
                                    $type_result = $conn->query($type_query);
                                    while ($type = $type_result->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($type['type']) . "'>" . htmlspecialchars($type['type']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- Material Filter -->
                            <div class="mb-1">
                                <label for="material" class="form-label">Color</label>
                                <select name="material" id="material" class="form-select">
                                    <option value="">Select Material</option>
                                    <?php
                                    // Fetch distinct colors from the database
                                    $type_query = "SELECT DISTINCT material FROM footwear_models";
                                    $type_result = $conn->query($type_query);
                                    while ($material = $type_result->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($material['material']) . "'>" . htmlspecialchars($material['material']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- Size Variation Filter -->
                            <div class="mb-1">
                                <label for="size_variation" class="form-label">Size Variation</label>
                                <select name="size_variation" id="size_variation" class="form-select">
                                    <option value="">Select Size Variation</option>
                                    <?php
                                    $size_variation_query = "SELECT DISTINCT size_variation FROM footwear_stock";
                                    $size_variation_result = $conn->query($size_variation_query);
                                    while ($size_variation = $size_variation_result->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($size_variation['size_variation']) . "'>" . htmlspecialchars($size_variation['size_variation']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Stock Availability Filter -->
                            <div class="mb-1">
                                <label for="stock_available" class="form-label">Stock Availability</label>
                                <select name="stock_available" id="stock_available" class="form-select">
                                    <option value="">Select Stock Availability</option>
                                    <option value="Yes">In Stock</option>
                                    <option value="No">Out of Stock</option>
                                </select>
                            </div>
                            <!-- Price Filter -->
                            <div class="mb-1">
                                <label for="price" class="form-label">Price</label>
                                <div class="row">
                                    <input type="number" name="min_price" class="form-control col ms-3 me-1 ps-1 text-center" placeholder="Min Price" min="0">
                                    <input type="number" name="max_price" class="form-control col me-3 ms-1 ps-1 text-center" placeholder="Max Price" min="0">
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary mt-1" id="submit_button" name="submit_buttons">Apply Filters</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Footwear List -->
            <div class=" col-md-8 col-xl-9 col-xxl-10 footwear_list">
                <?php if ($result->num_rows > 0): ?>
                <div class="row g-3">
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-sm-6 col-xl-4 col-xxl-3">
                        <div class="card shadow-sm">
                            <img src="data:<?php echo htmlspecialchars($row['image_type']); ?>;base64,<?php echo base64_encode($row['image_data']); ?>" alt="<?php echo htmlspecialchars($row['main_brand']); ?>" class="card-img-top rounded">
                            <div class="card-body pb-0">
                                <h6 class="card-title text-center">
                                <?php 
                                echo '<strong>'. htmlspecialchars($row['main_brand']) . ' ' . 
                                    (!empty($row['sub_brand']) ? htmlspecialchars($row['sub_brand']) . ' ' : '') . 
                                    htmlspecialchars($row['commodity']) . ' ' . htmlspecialchars($row['material']) .' ' . 
                                    htmlspecialchars($row['article']).' '. htmlspecialchars($row['color']).'</strong>'; 
                                ?>
                                </h6>
                                <p class="card-text">
                                    <strong>Type: </strong> <?php echo htmlspecialchars($row['type']); ?><br>
                                    <strong>Price: </strong> <?php echo htmlspecialchars($row['price']); ?><br>
                                    <strong>Size Variations: </strong> <?php echo htmlspecialchars($row['size_variation']); ?><br>
                                    <?php if($row['size_variation'] == 'Custom-Sizes'): ?>
                                    <strong>Custom Sizes:</strong><br>
                                    <?php 
                                        $sizes = explode(',', $row['custom_size']); // Assuming custom_size contains comma-separated sizes
                                        $stocks = explode(',', $row['stock']); // Assuming stock is also comma-separated
                                        for ($i = 0; $i < count($sizes); $i++) {
                                            echo 'Size: ' . htmlspecialchars($sizes[$i]) . ' - Stock: ' . htmlspecialchars($stocks[$i]) . '<br>';
                                        }
                                    ?>
                                    <?php else: ?>
                                    <strong>Stock Availability:</strong> <?php echo htmlspecialchars($row['stock']) ?><br>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="card-footer d-flex flex-column flex-xxl-row justify-content-center">
                                <a href="edit_footwear.php?model_id=<?php echo $row['model_id']; ?>" class="btn btn-sm btn-success mb-1 mb-xxl-0 me-1" style="font-size:13px;">Change Stock/Price</a>
                                <a href="remove_footwear.php?model_id=<?php echo $row['model_id']; ?>" style="font-size:13px;" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this model?');">Remove</a>
                            </div>
                        </div>  
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="alert alert-warning text-center d-flex justify-content-center align-items-center">
                    <p>No footwear models available. Add new models to display them here.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
