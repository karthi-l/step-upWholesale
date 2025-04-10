<?php
include('includes/session_dbConn.php');
$imageDirectory = "brand_img/";

$main_query = " SELECT * FROM brands WHERE sub_brand IS NULL ";
$main_brand = $conn->query($main_query);
$sub_query = "SELECT * FROM brands WHERE sub_brand IS NOT NULL";
$sub_brand = $conn->query($sub_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php 
    include('includes/inc_styles.php');
    ?>
    <style>
        .carousel {
            width: 35vw; /* Makes the carousel 80% of the viewport width */
        }

        .carousel-item{
            border-radius: 0.33rem;
        }
        .carousel-item img{
            width: 100%;
            height: 100%;
            object-fit:contain;
            border-radius: 0.33rem;
        }
    </style>
</head>
<body>  
    <!-- Including the main navbar -->
    <?php include('includes/main_nav.php'); ?>  
    <?php include('fetch_announcement.php'); ?>

<?php if (!empty($announcements)): ?>
  <div class="container mt-4">
    <div class="alert alert-info">
      <h5 class="mb-3">📢 Announcements</h5>
      <?php foreach ($announcements as $a): ?>
        <div class="mb-3 p-3 border rounded bg-light shadow-sm">
          <h6 class="mb-1"><?= htmlspecialchars($a['title']) ?></h6>
          <p class="mb-1"><?= nl2br(htmlspecialchars($a['message'])) ?></p>
          <small class="text-muted">🕒 Expires at: <?= date('d M Y, h:i A', strtotime($a['expires_at'])) ?></small>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>  
    <div class="container bg-light">
        <section class="hero row bg-light m-auto border  rounded  m-auto " id="hero-sec">
            <!-- Content Column -->
            <div class="hero-image col-lg-4 col-12 d-flex justify-content-center py-2">
                <img src="img/Walkaroo_logo.jpg" alt="Wholesale Footwear" class="border rounded-circle" width="192px">
            </div>
            <div class="hero-content col-lg-8 col-12 d-flex flex-column align-items-center text-center pt-3">
                <h1>Welcome to Saleem Traders</h1>
                <h2>It is a Step Up in Wholesale</h2>
                <p>Your trusted partner in wholesale footwear distribution.</p>
            </div>
        </section>
        <div class="row">
            <div class="col-6">
                <h2 class="display-6 display-xl-3 text-center">Brands we have dealership : </h2>
                <div id="carouselExampleControls1" class="carousel slide border rounded m-auto  mt-1 " data-bs-ride="carousel">
                    <?php if ($main_brand->num_rows > 0): ?>
                        <div class="carousel-inner">
                            <?php $isFirstItem = true; // Flag to mark the first item as active ?>
                            <?php while($row = $main_brand->fetch_assoc()): ?>
                                <div class="carousel-item <?php echo $isFirstItem ? 'active' : ''; ?>">
                                    <img src="<?php echo $imageDirectory . htmlspecialchars($row['image_file']); ?>" alt="<?php echo htmlspecialchars($row['main_brand']); ?>" class="d-block w-100">
                                </div>
                                <?php $isFirstItem = false; // Set flag to false after the first item ?>
                            <?php endwhile; ?>
                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleControls1" role="button" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleControls1" role="button" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-6">
                <h2 class="display-6 display-xl-3 text-center">Sub-brands we have dealership : </h2>
                <div id="carouselExampleControls2" class="carousel slide border rounded m-auto  mt-1 " data-bs-ride="carousel">
                    <?php if ($sub_brand->num_rows > 0): ?>
                        <div class="carousel-inner">
                            <?php $isFirstItem = true; // Flag to mark the first item as active ?>
                            <?php while($row = $sub_brand->fetch_assoc()): ?>
                                <div class="carousel-item <?php echo $isFirstItem ? 'active' : ''; ?>">
                                    <img src="<?php echo $imageDirectory . htmlspecialchars($row['image_file']); ?>" alt="<?php echo htmlspecialchars($row['main_brand']).htmlspecialchars($row['sub_brand']); ?>" class="d-block w-100">
                                </div>
                                <?php $isFirstItem = false; // Set flag to false after the first item ?>
                            <?php endwhile; ?>
                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleControls2" role="button" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleControls2" role="button" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </a>
                    <?php endif; ?>
                </div>         
            </div>
        </div>
        <section class="services py-5 bg-light">
            <div class="container">
                <div class="row text-center g-4">
                
                <!-- Card 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3 text-primary">
                        <i class="material-icons fs-1">local_shipping</i>
                        </div>
                        <h5 class="card-title">Delivery on Time</h5>
                        <p class="card-text">
                        Delivery on time every time, for every dispatch is our priority. We provide free van delivery to all our 
                        <span id="dots">...</span>
                        <span id="more" style="display: none;">
                            retailer shops. We stand proud in offering our unique combination of experience, service and technology to provide cost-effective logistic services with high reliability for every order dispatched anywhere nationally.
                        </span>
                        </p>
                        <button onclick="myFunction()" id="myBtn" class="btn btn-outline-primary btn-sm">Read more</button>
                    </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3 text-primary">
                        <i class="material-icons fs-1">payments</i>
                        </div>
                        <h5 class="card-title">Pricing</h5>
                        <p class="card-text">
                        No.1 Footwear Wholesale dealer specifically tailored to meet every budget and surety. The pricing 
                        <span id="dotsr">...</span>
                        <span id="read-more-text" style="display: none;">
                            is based on endurance assurance, finishing quality, and footwear feel — far beyond the worth of its sale value. We are principled to provide low quotes compared to other wholesale dealers.
                        </span>
                        </p>
                        <button onclick="readMore()" id="read-more" class="btn btn-outline-primary btn-sm">Read more</button>
                    </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3 text-primary">
                        <i class="material-icons fs-1">shopping_cart_checkout</i>
                        </div>
                        <h5 class="card-title">More Offers</h5>
                        <p class="card-text">
                        Circulated seasonal incentive schemes, discounts, and promotions are structured for 
                        <span id="offers-dots">...</span>
                        <span id="offers-more" style="display: none;">retailers twice a year for cost-effective mutual benefits.</span>
                        </p>
                        <button onclick="offersMore()" id="offers-btn" class="btn btn-outline-primary btn-sm">Read more</button>
                    </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3 text-primary">
                        <i class="material-icons fs-1">account_balance_wallet</i>
                        </div>
                        <h5 class="card-title">Supportable Payments</h5>
                        <p class="card-text">
                        Credit facilities are exclusively offered to all our customers based on turnover &amp; in 
                        <span id="payments-dots">...</span>
                        <span id="payments-more" style="display: none;">consideration with the volume of associated trades. We also provide payment support to our new customers.</span>
                        </p>
                        <button onclick="paymentsMore()" id="payments-btn" class="btn btn-outline-primary btn-sm">Read more</button>
                    </div>
                    </div>
                </div>

                </div>
            </div>
        </section>
        <div class="row">
            <div class="col-md-6">

            </div>
            <div class="col-md-6">
                
            </div>
        </div>
    </div>
<?php 
include('includes/inc_scripts.php');
?>
<script>
function myFunction() {
  var dots = document.getElementById("dots");
  var moreText = document.getElementById("more");
  var btnText = document.getElementById("myBtn");

  if (dots.style.display === "none") {
    dots.style.display = "inline";
    btnText.innerHTML = "Read more";
    moreText.style.display = "none";
  } else {
    dots.style.display = "none";
    btnText.innerHTML = "Read less";
    moreText.style.display = "inline";
  }
}

function readMore() {
  var dots = document.getElementById("dotsr");
  var moreText = document.getElementById("read-more-text");
  var btnText = document.getElementById("read-more");

  if (dots.style.display === "none") {
    dots.style.display = "inline";
    moreText.style.display = "none";
    btnText.innerHTML = "Read more";
  } else {
    dots.style.display = "none";
    moreText.style.display = "inline";
    btnText.innerHTML = "Read less";
  }
}

function offersMore() {
  var dots = document.getElementById("offers-dots");
  var moreText = document.getElementById("offers-more");
  var btnText = document.getElementById("offers-btn");

  if (dots.style.display === "none") {
    dots.style.display = "inline";
    moreText.style.display = "none";
    btnText.innerHTML = "Read more";
  } else {
    dots.style.display = "none";
    moreText.style.display = "inline";
    btnText.innerHTML = "Read less";
  }
}

function paymentsMore() {
  var dots = document.getElementById("payments-dots");
  var moreText = document.getElementById("payments-more");
  var btnText = document.getElementById("payments-btn");

  if (dots.style.display === "none") {
    dots.style.display = "inline";
    moreText.style.display = "none";
    btnText.innerHTML = "Read more";
  } else {
    dots.style.display = "none";
    moreText.style.display = "inline";
    btnText.innerHTML = "Read less";
  }
}
</script>
</body>
</html>