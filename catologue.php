<?php
include('db_connect.php'); // Include database connection

// Define directories
$imageDirectory = "brand_img/"; // Directory for brand images
$catalogueDirectory = "brand_catalogues/"; // Directory for catalogue files

// Fetch brand details from the database
$query = "SELECT brand_name, image_file, catalogue_file FROM brands"; // Adjust table name and columns as per your database
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Catalogue - Wholesale Footwear Management</title>
        <!-- Bootstrap 5 CSS -->
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body>
        <div class='container mt-5 row m-auto'>
            <div class='alert alert-warning text-center m-auto col-12 col-md-9 col-lg-7 col-xl-6'>
                <p>No catalogues available at the moment. Please check back later.</p>
                <div class='d-flex justify-content-center'>
                    <a href='index.php' class='btn btn-info mx-2'>Home</a>
                </div>
            </div>
         
        </div>
        <!-- Bootstrap 5 JS -->
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
            border-radius:0.33rem;
        }
        .brand-card .btn {
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
<div class="container d-flex justify-content-between align-items-center">
    <div class="mx-auto">
        <h1 class="mb-4">Brand Catalogues</h1>
    </div>
    <a href="index.php">
        <button class="btn btn-primary">Home</button>
    </a>
</div>


    
    <div class="row g-4">
    <?php foreach ($brands as $brand): ?>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="brand-card">
            <img src="<?php echo $imageDirectory . htmlspecialchars($brand['image_file']); ?>" alt="<?php echo htmlspecialchars($brand['brand_name']); ?>" class="image-fluid border border-rounded shadow-lg">
            <h5 class="mt-3"><?php echo htmlspecialchars($brand['brand_name']); ?></h5>
            <?php if (!empty($brand['catalogue_file'])): ?>
                <a href="<?php echo $catalogueDirectory . htmlspecialchars($brand['catalogue_file']); ?>" class="btn btn-primary me-2" target="_blank">View Catalogue</a>
                <a href="<?php echo $catalogueDirectory . htmlspecialchars($brand['catalogue_file']); ?>" class="btn btn-light border" download>Download Catalogue</a>
            <?php else: ?>
                <button class="btn btn-secondary" disabled>No Catalogue Available</button>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
