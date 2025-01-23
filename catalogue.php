<?php

include('session_dbConn.php'); // Include database connection

// Define directories
$imageDirectory = "brand_img/";
$catalogueDirectory = "brand_catalogues/";

// Get the filter from the query string (default to "all")
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build query based on the filter
switch ($filter) {
    case 'available':
        $query = "SELECT main_brand, image_file, catalogue_file, sub_brand FROM brands WHERE catalogue_file IS NOT NULL";
        break;
    case 'unavailable':
        $query = "SELECT main_brand, image_file, catalogue_file, sub_brand FROM brands WHERE catalogue_file IS NULL";
        break;
    default:
        $query = "SELECT main_brand, image_file, catalogue_file, sub_brand FROM brands";
        break;
}

// Fetch brand details from the database
$result = $conn->query($query);
if (!$result || $result->num_rows === 0) {
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Catalogue - Wholesale Footwear Management</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body>
        <div class='container mt-5 row m-auto'>
            <div class='alert alert-warning text-center m-auto col-12 col-md-9 col-lg-7 col-xl-6'>
                <p>No catalogues available for the selected filter. Please try a different filter.</p>
                <div class='d-flex justify-content-center'>
                    <a href='catalogue.php' class='btn btn-info mx-2'>Show All</a>
                    <a href='index.php' class='btn btn-primary mx-2'>Home</a>
                </div>
            </div>
        </div>
        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'></script>
    </body>
    </html>
    ";
    exit;
}

// Fetch data into an array
$brands = [];
while ($row = $result->fetch_assoc()) {
    $brands[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .brand-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            height: 100%;
        }
        .brand-card img {
            max-width: 100%;
            height: 150px;
            object-fit: contain;
            border-radius: 0.33rem;
        }
        .brand-card .btn {
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Brand Catalogues</h1>
        <a href="index.php" class="">
            <button class="btn btn-primary">Home</button>
        </a>
    </div>

    <!-- Filter Options -->
    <div class="d-flex justify-content-center mb-4">
        <a href="catalogue.php?filter=all" class="btn btn-outline-primary mx-2 <?php if ($filter == 'all') echo 'active'; ?>">Show All</a>
        <a href="catalogue.php?filter=available" class="btn btn-outline-success mx-2 <?php if ($filter == 'available') echo 'active'; ?>">Show Available</a>
        <a href="catalogue.php?filter=unavailable" class="btn btn-outline-danger mx-2 <?php if ($filter == 'unavailable') echo 'active'; ?>">Show Unavailable</a>
    </div>

    <div class="row g-4">
        <?php foreach ($brands as $brand): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="brand-card">
                    <img src="<?php echo $imageDirectory . htmlspecialchars($brand['image_file']); ?>" alt="<?php echo htmlspecialchars($brand['main_brand']); ?>" class="image-fluid border border-rounded shadow-lg">
                    <h5 class="mt-3"><?php echo htmlspecialchars($brand['main_brand']).' '.htmlspecialchars($brand['sub_brand']); ?></h5>
                    <?php if (!empty($brand['catalogue_file'])): ?>
                        <a href="<?php echo $catalogueDirectory . htmlspecialchars($brand['catalogue_file']); ?>" class="btn btn-primary me-2" target="_blank">View Catalogue</a>
                        <a href="<?php echo $catalogueDirectory . htmlspecialchars($brand['catalogue_file']); ?>" class="btn btn-light border" download>Download Catalogue</a>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>No Catalogue Available</button>
                        <button class="btn btn-warning" onclick="alert('We will notify you once the catalogue is available!');">Notify Me</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
