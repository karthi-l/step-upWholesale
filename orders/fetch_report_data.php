<?php
require '../includes/session_dbConn.php'; // Your database connection file

$type = $_GET['type'] ?? '';
$months = $_GET['months'] ?? 1;

function formatUserCard($user) {
    return "
    <div class='col-md-4 mb-3'>
        <div class='card h-100 shadow user-card' style='cursor: pointer;' data-userid='{$user['user_id']}'>
            <div class='card-body'>
                <h5 class='card-title'><i class='bi bi-person-circle'></i> {$user['username']}</h5>
                <h6 class='card-subtitle mb-2 text-muted'>{$user['shop_name']}</h6>
                <p class='card-text'>
                    <strong>Orders:</strong> {$user['total_orders']}<br>
                    <strong>Quantity:</strong> {$user['total_quantity']}<br>
                    <strong>Total Spent:</strong> ₹" . number_format($user['total_spent'], 2) . "
                </p>
            </div>
        </div>
    </div>";
}

function formatProductCard($product) {
    $imageSrc = "data:" . $product['image_type'] . ";base64," . base64_encode($product['image_data']);
    return "
    <div class='col-md-4 mb-3'>
        <div class='card h-100 shadow'>
            <img src='{$imageSrc}' class='card-img-top' style='height: 200px; object-fit: cover;' alt='Product Image'>
            <div class='card-body'>
                <h5 class='card-title'>{$product['main_brand']} - {$product['sub_brand']}</h5>
                <p class='card-text'>
                    <strong>Article:</strong> {$product['article']}<br>
                    <strong>Color:</strong> {$product['color']}<br>
                    <strong>Price:</strong> ₹" . number_format($product['price'], 2) . "<br>
                    <strong>Sold:</strong> {$product['total_quantity']} units<br>
                    <strong>Revenue:</strong> ₹" . number_format($product['total_sales'], 2) . "
                </p>
            </div>
        </div>
    </div>";
}



if ($type === 'user') {
// Top Users by Purchase
$query = "
    SELECT u.user_id, u.username, u.shop_name, 
           COUNT(DISTINCT o.order_id) AS total_orders,
           SUM(oi.quantity) AS total_quantity,
           SUM(oi.quantity * oi.unit_price) AS total_spent
    FROM usersretailers u
    JOIN orders o ON u.user_id = o.user_id
    JOIN order_items oi ON o.order_id = oi.order_id
    GROUP BY u.user_id
    ORDER BY total_spent DESC
    LIMIT 10
";


    $result = $conn->query($query);
    $users = $result->fetch_all(MYSQLI_ASSOC);

    echo "<div class='row'>";
    $count = 0;
    foreach ($users as $index => $user) {
        if ($index < 3 || isset($_GET['viewall'])) {
            echo formatUserCard($user);
        }
        $count++;
    }
    echo "</div>";

    if ($count > 3 && !isset($_GET['viewall'])) {
        echo "<button class='btn btn-outline-secondary view-more' data-type='user'>View More</button>";
    }
}

if ($type === 'product') {
// Top Products by Sales
$query = "
    SELECT fm.model_id, fm.main_brand, fm.sub_brand, fm.article, fm.color, fm.price, fm.image_name, fm.image_type, fm.image_data,
           SUM(oi.quantity) AS total_quantity,
           SUM(oi.quantity * oi.unit_price) AS total_sales
    FROM footwear_models fm
    JOIN order_items oi ON fm.model_id = oi.model_id
    GROUP BY fm.model_id
    ORDER BY total_sales DESC
    LIMIT 10
";

    $result = $conn->query($query);
    $products = $result->fetch_all(MYSQLI_ASSOC);

    echo "<div class='row'>";
    $count = 0;
    foreach ($products as $index => $product) {
        if ($index < 3 || isset($_GET['viewall'])) {
            echo formatProductCard($product);
        }
        $count++;
    }
    echo "</div>";

    if ($count > 3 && !isset($_GET['viewall'])) {
        echo "<button class='btn btn-outline-secondary view-more' data-type='product'>View More</button>";
    }
}

if ($type === 'date') {
    $stmt = $conn->prepare("
        SELECT 
        o.order_id,
        o.user_id,
        oi.model_id, 
        oi.quantity,
        SUM(oi.quantity) AS total_qty, 
        SUM(oi.quantity * oi.unit_price) AS total_sales,
        u.username, 
        u.shop_name,
        fm.commodity, 
        fm.article, 
        fm.color, 
        fm.image_data, 
        fm.image_type
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            JOIN usersretailers u ON o.user_id = u.user_id
            JOIN footwear_models fm ON oi.model_id = fm.model_id
            WHERE o.order_placed_time >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
            GROUP BY oi.model_id, o.user_id
            ORDER BY total_sales DESC
    ");
    $stmt->bind_param("i", $months);
    $stmt->execute();
    $result = $stmt->get_result();

    $productStats = [];
    $userStats = [];
    
    while ($row = $result->fetch_assoc()) {
        // Store product info
        if (!isset($productStats[$row['model_id']])) {
            $productStats[$row['model_id']] = [
                'qty' => 0,
                'sales' => 0,
                'commodity' => $row['commodity'],
                'article' => $row['article'],
                'color' => $row['color'],
                'image_data' => $row['image_data'],
                'image_type' => $row['image_type']
            ];
        }
        $productStats[$row['model_id']]['qty'] += $row['total_qty'];
        $productStats[$row['model_id']]['sales'] += $row['total_sales'];
    
        // Store user info
        if (!isset($userStats[$row['user_id']])) {
            $userStats[$row['user_id']] = [
                'sales' => 0,
                'qty' => 0,
                'orders' => [],
                'username' => $row['username'],
                'shopname' => $row['shop_name']
            ];
        }
        
        $userStats[$row['user_id']]['sales'] += $row['total_sales'];
        $userStats[$row['user_id']]['qty'] += $row['total_qty'];
        
        // Track unique orders
        if (!in_array($row['order_id'], $userStats[$row['user_id']]['orders'])) {
            $userStats[$row['user_id']]['orders'][] = $row['order_id'];
        }
    }        
    arsort($productStats);
    arsort($userStats);

    echo "<h4>📦 Most Sold Products</h4><div class='row'>";
    foreach (array_slice($productStats, 0, 3, true) as $pid => $stat) {
        $img = 'data:' . $stat['image_type'] . ';base64,' . base64_encode($stat['image_data']);
        echo "
        <div class='col-md-4 mb-3'>
            <div class='card h-100 shadow'>
                <img src='{$img}' class='card-img-top' alt='Product Image'>
                <div class='card-body'>
                    <h5 class='card-title'>🆔 Model ID: {$pid}</h5>
                    <p class='card-text'>
                        <strong>Commodity:</strong> {$stat['commodity']}<br>
                        <strong>Article:</strong> {$stat['article']}<br>
                        <strong>Color:</strong> {$stat['color']}<br>
                        <strong>Quantity:</strong> {$stat['qty']}<br>
                        <strong>Sales:</strong> ₹" . number_format($stat['sales']) . "
                    </p>
                </div>
            </div>
        </div>";
    }
    echo "</div><hr>";
    
    echo "<h4>👥 Top Users (By Purchase)</h4><div class='row'>";
    foreach (array_slice($userStats, 0, 3, true) as $uid => $stat) {

        $totalOrders = count($stat['orders']);
        echo "
        <div class='col-md-4 mb-3'>
            <div class='card h-100 shadow user-card' data-userid='{$uid}'>
                <div class='card-body'>
                    <h5 class='card-title'>👤 {$stat['username']}</h5>
                    <p class='card-text'>
                        <strong>Shop:</strong> {$stat['shopname']}<br>
                        <strong>Total Orders:</strong> {$totalOrders}<br>
                        <strong>Total Quantity:</strong> {$stat['qty']}<br>
                        <strong>Total Spent:</strong> ₹" . number_format($stat['sales']) . "
                    </p>
                </div>
            </div>
        </div>";
        
    }
    echo "</div>";
}
?>