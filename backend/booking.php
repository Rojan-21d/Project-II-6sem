<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/sweetAlert.css">
    <script src="../js/sweetalert.js"></script>
    <title>Booking</title>
</head>
<body>
<?php
// Check if the session has not started, then start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Check if the user is not logged in
if(!isset($_SESSION['email'])) {
    // Redirect the user to the login page or any other authentication page
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['action']) && isset($_POST['load_id']) && isset($_POST['consignor_id']) && isset($_POST['carrier_id'])) {
    $load_id = $_POST['load_id'];
    require 'databaseconnection.php';

    try {
        // Begin the transaction
        $conn->begin_transaction();

        $sql1 = "UPDATE loaddetails SET status = 'booked' WHERE id = '$load_id'";
        $sql2 = "INSERT INTO shipment (load_id, carrier_id, consignor_id) VALUES ('".$_POST['load_id']."', '".$_POST['carrier_id']."', '".$_POST['consignor_id']."')";
        
        // Execute the statements
        $conn->query($sql1);
        $conn->query($sql2);
        
        // Commit the transaction
        $conn->commit();
        
        echo '<script>
            Swal.fire({
                title: "Booking Successful",
                text: "Your load has been booked successfully.",
                icon: "success",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "../home.php";
                }
            });
        </script>';
        exit;
    } catch (Exception $e) {
        // Rollback in case of error
        $conn->rollback();
        echo '<script>
            Swal.fire({
                title: "Error",
                text: "Error: ' . $e->getMessage() . '",
                icon: "error",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "../home.php";
                }
            });
        </script>';
        exit;
    }
}
?>
</body>
</html>