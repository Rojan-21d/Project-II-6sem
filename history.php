<?php
// Check if the session has not started, then start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    // Check if the user is not logged in
    if (!isset($_SESSION['email'])) {
        // Redirect the user to the login page or any other authentication page
        header("Location: login.php");
        exit;
    }

    // Database connection
    require 'backend/databaseconnection.php';
    include 'layout/header.php';

    $sql = "SELECT loaddetails.id, loaddetails.name, loaddetails.img_srcs, loaddetails.dateofpost, loaddetails.status, shipment.id AS shipment_id
        FROM loaddetails 
        INNER JOIN shipment ON loaddetails.id = shipment.load_id 
        WHERE shipment.carrier_id = '" . $_SESSION['id'] . "'";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" href="css/headerfooterstyle.css">
    <link rel="stylesheet" href="css/maincontentstyle.css">
</head>
<body>
    <div class="congmain">
        <div class="table-container">
            <div class="head">
                <h2>Your History</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th width="2%">S.N.</th>
                        <th width="40%">Name</th>
                        <th width="5%">Photo</th>
                        <th width="10%">Date of Uploaded</th>
                        <th width="15%">Status</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $i = 1; // Initialize $i variable
                        while ($row = $result->fetch_assoc()) {
                            $load_id = $row['id'];
                            $shipment_id = $row['shipment_id'];

                            $stat = ($row['status'] == "booked") ? "Booked" : (($row['status'] == "delivered") ? "Delivered" : "");

                            echo "<tr class='tr-bottom'>
                            <td>" . $i . "</td>
                            <td>" . $row['name'] . "</td>
                            <td><img src='" . $row['img_srcs'] . "' > </td>
                            <td>" . $row['dateofpost'] . "</td><td>";
                            if ($row['status'] == "booked") {
                                echo "<p class='status booked'>Booked</p>";
                            } elseif ($row['status'] == "delivered") {
                                echo "<p class='status delivered'>Delivered</p>";
                            }
                            echo"</td><td>
                                <div class='td-center'>
                                    <form action='backend/moredeleteload.php' method='post' class='moreBtn'>
                                        <input type='hidden' name='action' value='more'>
                                        <input type='hidden' name='id' value='" . $load_id . "'>
                                        <input type='hidden' name='shipment_id' value='" . $shipment_id . "'>
                                        <button type='submit' id='more'>More</button>
                                    </form>";
                        echo "</div>
                            </td>
                        </tr>";
                        
                            $i++; // Increment $i after each iteration
                        }
                    } else {
                        echo "<tr><td colspan='6'>No Records Found</td></tr>";                    
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
        include 'layout/footer.php';
    ?>
    <script src="js/confirmationSA.js"></script>
</body>
</html>
