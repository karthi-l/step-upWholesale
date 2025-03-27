<?php
include('../includes/session_dbConn.php');

$user_id = $_SESSION['user_id'];
$query = "
    SELECT fm.model_id, 
           fm.main_brand, 
           fm.sub_brand, 
           fm.commodity, 
           fm.article, 
           fm.color, 
           fm.price, 
           fm.stock_available, 
           fm.image_type, 
           fm.image_data, 
           fm.type, 
           fm.material, 
           GROUP_CONCAT(DISTINCT fs.size_variation ORDER BY fs.size_variation) AS size_variation,
           GROUP_CONCAT(DISTINCT fs.custom_size ORDER BY fs.custom_size) AS custom_size,
           GROUP_CONCAT(fs.stock) AS stock,
           uc.model_id AS cart_model_id, 
           uc.user_id, 
           uc.quantity
    FROM footwear_models fm
    LEFT JOIN footwear_stock fs ON fm.model_id = fs.model_id
    LEFT JOIN user_cart uc ON fm.model_id = uc.model_id 
    WHERE uc.user_id = ?  -- Use '?' placeholder for MySQLi prepared statement
    GROUP BY fm.model_id
    ORDER BY fm.main_brand, fm.sub_brand;
";

$stmt = $conn->prepare($query);

// Bind the parameter to the statement
$stmt->bind_param("i", $user_id);  // 'i' for integer as user_id is likely an integer

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();


$stmt->close();

$userQuery = "SELECT username, shop_name, shop_address, mobile_number, gstin, owner_name, userType FROM usersretailers WHERE user_id = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userRow = $userResult->fetch_assoc();
$userStmt->close();

$discountPercentage = 0;
if ($userRow) {
    switch ($userRow['userType']) {
        case 'Regular':
            $discountPercentage = 30;
            break;
        case 'Frequent':
            $discountPercentage = 32;
            break;
        case 'VIP':
            $discountPercentage = 34;
            break;
    }
}

// Calculate expected delivery date (next Saturday)
$today = new DateTime();
do {
    $today->modify('+1 day');
} while ($today->format('l') !== 'Saturday');
$expected_delivery = $today->format('Y-m-d');

function generateInvoiceNo(){
    return rand(1000,9999);
}
function generateBillNo(){
    return rand(1000,9999);
}
function generateOrderNo(){
    return rand(1000,9999); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body { font-family: Arial, 'DejaVu Sans', 'Noto Sans', sans-serif; }
        .invoice-container {  margin: auto; border: 1px solid #ddd; padding: 20px; }
        .header { text-align: center; }
        .header img { max-width: 100px; }
        .header h2 { margin: 5px 0; }
        .details-table-1, .product-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            table-layout: fixed; /* Ensures the columns don't resize */
        }
        .details-table-2 {
            width: 25%; /* Ensure it's consistent with the space it occupies */
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }
        .details-table-1 td, .details-table-2 td, .product-table td, .product-table th { 
            border: 1px solid #ddd; 
            padding: 6px; 
            text-align: left;
            word-wrap: break-word; /* Ensures long text doesn't break the layout */
        }
        .details-table-1 td, .details-table-2 td, .product-table td, .product-table th{
          
            font-weight:100px;
        }
        .product-table th { 
            background-color: #f2f2f2; /* Optional: Add background to header */
        }
        .text-right { text-align: right; }
        
        /* Optional: Define specific widths for columns in product table */
        .product-table th:nth-child(1), .product-table td:nth-child(1) { width: 7%; }
        .product-table th:nth-child(2), .product-table td:nth-child(2) { width: 50%; }
        .product-table th:nth-child(3), .product-table td:nth-child(3) { width: 8%; }
        .product-table th:nth-child(4), .product-table td:nth-child(4) { width: 10%; }
        .product-table th:nth-child(5), .product-table td:nth-child(5) { width: 10%; }
        .product-table th:nth-child(6), .product-table td:nth-child(6) { width: 15%; }
        
        /* Adjusting details table to match with product table's last two columns */
      
        
        .totalsSection{
            display:flex;
            justify-content:flex-end;
        }
        .header{
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
    </style>
    
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <img src="../img/st-logo.png" style="border-radius:.33rem;"alt="Shop Logo">
            <h2>SALEEM TRADERS</h2>
            <h3>GSTIN:33ABZPF1979R1ZL</h3>
        </div>
        <div class="">
            <p>No.201, Raja Mill Road, Pollachi 642001</p>
        </div>
        <hr>
        <table class="details-table-1">
            <tr>
                <td><strong>Invoice No: <?php echo generateInvoiceNo();?></strong></td>
                <td><strong>Bill No: <?php echo generateBillNo();?></strong></td>
                <td><strong>Order No: <?php echo generateOrderNo();?></strong></td>
            </tr>
            <tr>
                <td><strong>GSTIN: <?php echo $userRow['gstin'] ? $userRow['gstin'] : ''; ?></td>
                <td><strong>Bill To: </strong><?php echo $userRow['shop_name'];?></td>
                <td><strong>Ordered By: </strong><?php echo $userRow['username'];?></td>
            </tr>
            <tr>
                <td><strong>Mobile: </strong><?php echo $userRow['mobile_number']?></td>
                <td><strong>Expected Delivery: </strong><?php echo $expected_delivery;?></td>
                <td><strong>Date: </strong><?php echo $today->format('Y-m-d');?></td>
            </tr>
            <tr>
               <td colspan="3"><strong>Shop Address: </strong><?php echo $userRow['shop_address'];?></td> 
            </tr>
        </table>

        <table class="product-table">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Product Description</th>
                    <th>CD%</th>
                    <th>Net Rate</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                $totalnos= 0;
                $total_amount = 0;
                while ($products = $result->fetch_assoc()) { 
                    $netrate = $products['price'] - ($products['price'] * ($discountPercentage / 100));
                    $total = $netrate * $products['quantity'];
                    $total_amount += $total;
                    $totalnos += $products['quantity'];
                ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo htmlspecialchars($products['main_brand']) . '-' . 
                        (!empty($products['sub_brand']) ? htmlspecialchars($products['sub_brand']) . ' (' : ' (') .  // Ternary operator fixed
                        htmlspecialchars($products['type']) . ') ' . 
                        htmlspecialchars($products['article']) . ' ' . 
                        htmlspecialchars($products['color']). ' - '. 
                        htmlspecialchars($products['price']);
                    ?></td>
                    <td><?php echo $discountPercentage;?>%</td>
                    <td><?php echo $netrate;?></td>
                    <td><?php echo $products['quantity'];?></td>
                    <td><?php echo $total;?></td>
                </tr>
                <?php $i+=1; ?>
                <?php } ?>
            </tbody>
        </table>
        <div class="totalsSection">
            <table class="details-table-2">
                <tr>
                    <td><strong>Total: </strong><?php echo $totalnos;?> Pair</td>
                    <td class="text-right">₹<?php echo number_format($total_amount, 2); ?></td>
                </tr>
                <tr>
                    <td ><strong>CGST (6%):</strong></td>
                    <td class="text-right">₹<?php echo number_format($total_amount * 0.06, 2); ?></td>
                </tr>
                <tr>
                    <td ><strong>SGST (6%):</strong></td>
                    <td class="text-right">₹<?php echo number_format($total_amount * 0.06, 2); ?></td>
                </tr>
                <tr>
                    <td ><strong>Grand Total:</strong></td>
                    <td class="text-right"><strong>₹<?php echo number_format($total_amount * 1.12, 2); ?></strong></td>
                </tr>
            </table>
        </div>
        <button class="btn btn-primary mx-2"><a href="user_cart.php" class="text-white" style="text-decoration:none;">Back</a></button>
        <button class="btn btn-primary mx-2"><a href="generate_pdf_invoice.php" class="text-white" style="text-decoration:none;">Confirm Order</a></button>
    </div>
    
</body>
</html>
