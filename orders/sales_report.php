<?php
include("../includes/session_dbConn.php");

// Ensure only admin has access
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

function getTopUsers($conn, $limit = 3) {
    $query = "
        SELECT o.user_id, SUM(oi.quantity * oi.unit_price) as total_spent, COUNT(o.order_id) as total_orders
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        GROUP BY o.user_id
        ORDER BY total_spent DESC
        LIMIT $limit
    ";
    return $conn->query($query)->fetch_all(MYSQLI_ASSOC);
}

function getTopProducts($conn, $limit = 3) {
    $query = "
        SELECT oi.model_id, SUM(oi.quantity) as total_qty, SUM(oi.quantity * oi.unit_price) as total_sales
        FROM order_items oi
        GROUP BY oi.model_id
        ORDER BY total_sales DESC
        LIMIT $limit
    ";
    return $conn->query($query)->fetch_all(MYSQLI_ASSOC);
}

function getUserStats($conn, $user_id) {
    $query = "
        SELECT o.user_id, COUNT(DISTINCT o.order_id) AS total_orders, 
               MAX(o.order_placed_time) AS last_order,
               SUM(oi.quantity * oi.unit_price) AS total_spent,
               oi.model_id, SUM(oi.quantity) AS product_qty
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY oi.model_id
        ORDER BY product_qty DESC
        LIMIT 1
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getMonthlyReport($conn, $months = 1) {
    $query = "
        SELECT oi.model_id, SUM(oi.quantity) as total_qty, 
               o.user_id, SUM(oi.quantity * oi.unit_price) as total_sales
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.order_placed_time >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
        GROUP BY oi.model_id, o.user_id
        ORDER BY total_sales DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $months);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>    
    <title>Sales Report</title>
    <?php include('../includes/inc_styles.php');?>
</head>

<body class="container py-4">

    <div class="d-flex justify-content-center align-items-center mb-4">
        <h2 class="mb-4">📊 Sales Report Dashboard</h2>
        <a href="../index.php" class="btn btn-primary ms-auto"><i class="bi bi-house-door-fill"></i> Home</a>
    </div>

    <div class="mb-4">
        <div class="btn-group" role="group">
            <button class="btn btn-outline-primary filter-btn" data-type="user">Top Users</button>
            <button class="btn btn-outline-success filter-btn" data-type="product">Top Products</button>
            <button class="btn btn-outline-warning filter-btn" data-type="date">Monthly Reports</button>
        </div>
    </div>

    <div id="filterOptions" class="mb-4"></div>
    <div id="reportContent"></div>

    <!-- User Stats Modal -->
    <div class="modal fade" id="userStatsModal" tabindex="-1" aria-labelledby="userStatsLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="userStatsLabel">User Statistics</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" id="userStatsContent">
            <!-- User details will be populated here -->
          </div>
        </div>
      </div>
    </div>
<?php include('../includes/inc_scripts.php');?>
<script>
$(function () {
    $('.filter-btn').on('click', function () {
        const type = $(this).data('type');
        $('#filterOptions').html('');
        $('#reportContent').html('Loading...');

        if (type === 'user') {
            $.get('fetch_report_data.php?type=user', function (data) {
                $('#reportContent').html(data);
            });
        } else if (type === 'product') {
            $.get('fetch_report_data.php?type=product', function (data) {
                $('#reportContent').html(data);
            });
        } else if (type === 'date') {
            $('#filterOptions').html(`
        <select id="dateRange" class="form-select w-auto d-inline">
            <option value="1">Current Month</option>
            <option value="3">Last 3 Months</option>
            <option value="6">Last 6 Months</option>
            <option value="12">Last Year</option>
        </select>
        <button class="btn btn-primary ms-2" id="fetchDateReport">Go</button>
    `);

    // Auto-load current month data
    $('#reportContent').html('Loading...');
    $.get('fetch_report_data.php?type=date&months=1', function (data) {
        $('#reportContent').html(data);
    });

        }
    });

    $(document).on('click', '#fetchDateReport', function () {
        const months = $('#dateRange').val();
        $('#reportContent').html('Loading...');
        $.get('fetch_report_data.php?type=date&months=' + months, function (data) {
            $('#reportContent').html(data);
        });
    });

    $(document).on('click', '.user-card', function () {
        const userId = $(this).data('userid');
        $.get('fetch_user_stats.php?user_id=' + userId, function (data) {
            $('#userStatsContent').html(data);
            new bootstrap.Modal(document.getElementById('userStatsModal')).show();
        });
    });
    $(document).on('click', '.view-more', function () {
        const type = $(this).data('type');
        $.get('fetch_report_data.php?type=' + type + '&viewall=1', function (data) {
            $('#reportContent').html(data);
        });
    });
    $('.filter-btn[data-type="user"]').trigger('click');

});

</script>

</body>
</html>
