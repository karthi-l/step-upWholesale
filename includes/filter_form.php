<form method="POST" action="products.php" class="dropdown-item bg-light">
    <h1 class="text-center bg-info text-white w-50 m-auto mb-2 border rounded d-md-none">Filter</h1>
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