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
// Step 1: Start session if it's not started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Step 2: Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit;
}

// Step 3: Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['load_id']) && isset($_POST['consignor_id']) && isset($_POST['carrier_id'])) {
    $load_id = $_POST['load_id'];
    $carrier_id = $_POST['carrier_id'];
    $consignor_id = $_POST['consignor_id'];

    // Step 4: Include database connection
    require 'databaseconnection.php';

    try {
        // Step 5: Start transaction
        $conn->begin_transaction();

        // Step 6: Update load status
        $sql1 = "UPDATE loaddetails SET status = 'booked' WHERE id = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param('i', $load_id);
        $stmt1->execute();

        // Step 7: Insert shipment record
        $sql2 = "INSERT INTO shipment (load_id, carrier_id, consignor_id) VALUES (?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param('iii', $load_id, $carrier_id, $consignor_id);
        $stmt2->execute();

        // Step 8: Commit transaction
        $conn->commit();

        // Step 9: Show success message with SweetAlert
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
        // Step 10: Rollback on error and show error message
        $conn->rollback();
        echo '<script>
            Swal.fire({
                title: "Error",
                text: "Booking failed: ' . $e->getMessage() . '",
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
} else {
    // If form submission is not valid, show error message
    echo '<script>
        Swal.fire({
            title: "Error",
            text: "Invalid form submission",
            icon: "error",
            confirmButtonText: "OK"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "../home.php";
            }
        });
    </script>';
}
?>

</body>
</html>
