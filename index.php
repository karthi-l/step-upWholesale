<?php 
session_start();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="app.css ">
</head>
<body>  
            <nav class="navbar navbar-dark fixed-top navbar-expand-lg bg-dark  rounded p-1 shadow-sm">
                <div class="container-fluid d-flex align-items-center">
                    <a href="index.php" class="navbar-brand">
                        <img src="img/st-logo.png" alt="" id="nav-logo" class="img-fluid rounded d-inline-block border border-white" >
                    </a>
                    <div class="d-flex align-items-center justify-content-center">
                        <a href="account.php" class="d-block d-lg-none mx-1 border rounded border-white">
                            <img src="img/account-logo.png" alt="Account" class="img-fluid hov-nav nav-align" width=39px>
                        </a>
                        <button class="navbar-toggler mx-1 border border-white hov-nav" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>
                    

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item  mx-1 border border-white rounded my-1 my-lg-0"><a class="nav-link active text-light hov-nav nav-align" href="index.php">Home</a></li>
                            <li class="nav-item  mx-1 border border-white rounded my-1 my-lg-0"><a href="about.php" class="nav-link active text-light hov-nav nav-align">About-Us</a></li>
                            <li class="nav-item  mx-1 border border-white rounded my-1 my-lg-0"><a href="products.php" class="nav-link active text-light hov-nav nav-align">Products</a></li>
                            <li class="nav-item  mx-1 border border-white rounded my-1 my-lg-0"><a href="catologue.php" class="nav-link active text-light hov-nav nav-align">Catologue</a></li>
                            <li class="nav-item  mx-1 my-1 my-lg-0">
                                <form class="d-flex ">
                                    <input class="form-control me-2 nav-align" type="search" placeholder="Search" aria-label="Search">
                                    <button class="btn btn-outline-success border border-white text-white nav-align" type="submit">Search</button>
                                  </form>
                            </li>
                            <li class="nav-item mx-1 border border-white rounded d-none d-lg-block"><a href="account.php" class="nav-link active hov-nav nav-align"><img src="img/account-logo.png" alt="Account" class="img-fluid" width=20px></a></li>
                            <li class="nav-item mx-1 d-block d-lg-none my-1 my-lg-0">    

                        </ul>
                    </div>
                </div>
            </nav>    
            <section class="hero row bg-light m-auto border rounded" id="hero-sec">
                <!-- Content Column -->

                <div class="hero-image col-lg-4 col-12 d-flex justify-content-center py-2">
                  <img src="img/Walkaroo_logo.jpg" alt="Wholesale Footwear" class="border rounded-circle" width="192px">
                </div>


                <div class="hero-content col-lg-8 col-12 d-flex flex-column align-items-center text-center pt-3">
                  <h1>Welcome to Saleem Traders</h1>
                  <h2>It is a Step Up in Wholesale</h2>
                  <p>Your trusted partner in wholesale footwear distribution.</p>
                  <div class="hero-buttons d-flex justify-content-center">
                    <a href="catologue.php" class="btn btn-primary mx-2 mb-2 mb-lg-0">Explore Products</a>
                   
                  </div>
                </div>
              
               
                
              </section>
              
              
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
