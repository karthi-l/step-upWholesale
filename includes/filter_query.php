<?php

// Build dynamic query based on filters
$whereClauses = [];

// Change $_GET to $_POST to receive data via POST
if (isset($_POST['search']) && !empty($_POST['search'])) {
    $searcharray = $conn->real_escape_string($_POST['search']);
    $array = explode(' ', $searcharray); // Split by a single space
    foreach ($array as $search) {
        $whereClauses[] =  "(
            fm.main_brand LIKE '%" . $search . "%' OR
            fm.sub_brand LIKE '%" . $search . "%' OR
            fm.commodity LIKE '%" . $search . "%' OR
            fm.article LIKE '%" . $search . "%' OR
            fm.color LIKE '%" . $search . "%' OR
            fm.price LIKE '%" . $search . "%' OR
            fm.stock_available LIKE '%" . $search . "%' OR
            fm.type LIKE '%" . $search . "%' OR
            fm.material LIKE '%" . $search . "%' OR
            fs.size_variati on LIKE '%" . $search . "%' OR
            fs.custom_size LIKE '%" . $search . "%') 
        ";
    }
}
if (isset($_POST['main_brand']) && !empty($_POST['main_brand'])) {
    $whereClauses[] = "fm.main_brand = '" . $conn->real_escape_string($_POST['main_brand']) . "'";
}
if (isset($_POST['sub_brand']) && !empty($_POST['sub_brand'])) {
    $whereClauses[] = "fm.sub_brand = '" . $conn->real_escape_string($_POST['sub_brand']) . "'";
}
if (isset($_POST['color']) && !empty($_POST['color'])) {
    $whereClauses[] = "fm.color = '" . $conn->real_escape_string($_POST['color']) . "'";
}
if (isset($_POST['commodity']) && !empty($_POST['commodity'])) {
    $whereClauses[] = "fm.commodity = '" . $conn->real_escape_string($_POST['commodity']) . "'";
}
if (isset($_POST['stock_available']) && !empty($_POST['stock_available'])) {
    // We join with footwear_stock table to filter by stock availability
    $whereClauses[] = "fm.stock_available = '" . $conn->real_escape_string($_POST['stock_available']) . "'";
}
if (isset($_POST['min_price']) && is_numeric($_POST['min_price'])) {
    $whereClauses[] = "fm.price >= " . (int)$_POST['min_price'];
}
if (isset($_POST['max_price']) && is_numeric($_POST['max_price'])) {
    $whereClauses[] = "fm.price <= " . (int)$_POST['max_price'];
}
if (isset($_POST['type']) && !empty($_POST['type'])) {
    $whereClauses[] = "fm.type = '" . $conn->real_escape_string($_POST['type']) . "'";
}
if (isset($_POST['material']) && !empty($_POST['material'])) {
    $whereClauses[] = "fm.material = '" . $conn->real_escape_string($_POST['material']) . "'";
}
if (isset($_POST['size_variation']) && !empty($_POST['size_variation'])) {
    $whereClauses[] = "fs.size_variation = '" . $conn->real_escape_string($_POST['size_variation']) . "'";
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
