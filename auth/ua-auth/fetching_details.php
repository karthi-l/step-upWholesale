<?php 

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM usersretailers WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userDetails = $result->fetch_assoc(); // Store the details
    } else {
        echo "
            <div class='container text-center mt-5'>
                <div class='alert alert-warning m-auto text-center'>
                    <h4>Unable to fetch your details. Please contact support.</h4>
                    <div class='d-flex justify-content-center'>
                        <a href='../logout.php' class='btn btn-primary mx-2'>Logout</a>
                        <a href='../../index.php' class='btn btn-info mx-2'>Home</a>
                    </div>
                </div>
            </div>
        ";
        exit;
    }

} elseif (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    // Use prepared statements to prevent SQL injection
    $query = "SELECT * FROM admins WHERE admin_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $adminDetails = $result->fetch_assoc(); // Store the details
    } else {
        echo "
            <div class='container text-center mt-5'>
                <div class='alert alert-warning m-auto text-center'>
                    <h4>Unable to fetch your details. Please contact support.</h4>
                    <div class='d-flex justify-content-center'>
                        <a href='../logout.php' class='btn btn-primary mx-2'>Logout</a>
                        <a href='../../index.php' class='btn btn-info mx-2'>Home</a>
                    </div>
                </div>
            </div>
        ";
        exit;
    }
}
?>
