
<nav class="navbar navbar-dark fixed-top navbar-expand-lg bg-dark shadow- sm py-md-0 ">
    <?php 
        // Check if admin_id is set in the session
    if (isset($_SESSION['admin_id'])):
        $dashboard_link = "../auth/admin-portal/admin_dashboard.php";  // Admin's dashboard
        $headtag = "Footwear Stock"; //Heading Tag
        $process = "Add New Model"; //Process Button Text
        $processpage = "upload_footwear.php"; //Upload footwear page link
    elseif (isset($_SESSION['user_id'])):
        $dashboard_link = "../auth/user-portal/user_dashboard.php";   // User's dashboard
        $headtag = "Footwear Models"; // Heading tag
        $process = "Cart"; //Process Button Text
        $processpage = "user_cart.php"; //users cart's page link
    else:
        $dashboard_link = "../auth/dashboard.php";         // Default dashboard
    endif;
    ?>
    <div class="container-fluid d-flex align-items-center d-flex align-items-center flex-row ">
        <a href="index.php" class="navbar-brand d-none d-md-block">
            <img src="../img/st-logo.png" alt="st-logo" id="nav-logo" class="img-fluid rounded d-inline-block border border-white" >
        </a>
        <div class="dropdown-show d-md-none">
            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Filter
            </button>
            <div class="dropdown-menu mt-2">
                <?php include('../includes/filter_form.php'); ?>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-center">
            
            <a href="<?php echo $dashboard_link; ?>" class="d-block d-lg-none border rounded border-white">
                <img src="../img/account-logo.png" alt="Account" class="img-fluid hov-nav nav-align" width=39px>
            </a>
            <button class="navbar-toggler mx-1 mx-xs-0 border border-white hov-nav" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>         
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item  mx-1 border border-white rounded my-1 my-lg-0"><a href="../index.php" class="nav-link active text-light hov-nav nav-align">Home</a></li>
                <li class="nav-item  mx-1 border border-white rounded my-1 my-lg-0"><a href="../about.php" class="nav-link active text-light hov-nav nav-align">About-Us</a></li>
                <li class="nav-item  mx-1 border border-white rounded my-1 my-lg-0"><a href="../products/catalogue.php" class="nav-link active text-light hov-nav nav-align">Catalogue</a></li>
                <li class="nav-item mx-1 border border-white rounded d-none d-lg-block"><a href="<?php echo $dashboard_link; ?>" class="nav-link active hov-nav nav-align"><img src="../img/account-logo.png" alt="Account" class="img-fluid" width=20px></a></li>    
            </ul>
        </div>
    </div>
</nav>    
<div class="container-fluid mt-3">
    
    <div class="row">
        <!-- Filter Sidebar -->
        <div class="d-none d-md-block col-md-6 col-lg-4 col-xl-3 col-xxl-2 sticky_filter">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-center border rounded bg-info text-white py-1">Filter</h5>
                    <?php include('../includes/filter_form.php'); ?>
                </div>
            </div>
        </div>
        <!-- Footwear List -->
        <div class=" col-md-6 col-lg-8 col-xl-9 col-xxl-10 footwear_list d-flex flex-column ">
            <?php if(isset($_SESSION['admin_id']) || isset($_SESSION['user_id'])) : ?>    
                <div class="d-flex justify-content-between align-items-center mb-1 w-100">
                    <h1 class=""><?php echo $headtag; ?></h1>
                    <div class="d-flex align-items-center justify-content-center">
                        <a href="<?php echo $processpage;?>" class="btn btn-primary mx-1" style="width:120%"><?php echo $process;?></a>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($result->num_rows > 0): ?>
            <div class="row g-3">
                <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-sm-6 col-md-12 col-lg-6 col-xl-4 col-xxl-3" id="product-card-<?php echo $row['model_id']; ?>"   >
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
                            <?php if(isset($_SESSION['admin_id'])): ?>
                                <a href="edit_footwear.php?model_id=<?php echo $row['model_id']; ?>" class="btn btn-sm btn-success mb-1 mb-xxl-0 me-1" style="font-size:13px;">Change Stock/Price</a>
                                <button class="btn btn-sm btn-danger remove-btn" style="font-size:13px;" data-model-id="<?php echo $row['model_id']; ?>">Remove</button>
                            <?php elseif(isset($_SESSION['user_id'])): ?>
                                <div class="card-footer d-flex flex-column flex-xxl-row justify-content-center align-items-center">
                                    <!-- Quantity Input -->
                                                                
                                    <!-- Cart Button -->
                                    <button class="btn btn-light add-to-cart ms-2 " data-model-id="<?php echo $row['model_id']; ?>" id="add-to-cart-<?= $row['model_id'] ?>" >
                                        <img src="../img/cart.png" alt="cart symbol" style="width:20px;">
                                    </button>

                                    <!-- Success Tick -->
                                    <span id="cart-status-<?php echo $row['model_id']; ?>" class="mx-1"></span> 

                                    <button class="remove-from-cart btn btn-light" data-model-id="<?= $row['model_id']; ?>" id="remove-from-cart-<?= $row['model_id'] ?>"  >
                                        <img src="../img/cart-rm-logo.png" style="width:20px;" alt="">
                                    </button>
                                    <span id="cart-status-<?= $row['model_id'] ?>" class="mx-1"></span>                              
                                </div>
                            <?php else: ?>
                                <div class="card-footer d-flex flex-column flex-xxl-row justify-content-center">You Can't Order!</div>
                            <?php endif; ?>
                        </div>
                    </div>  
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="d-flex justify-content-center align-items-center flex-grow-1">
                <div class="alert alert-info text-center">
                    <p class="m-0">No footwear models available.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
