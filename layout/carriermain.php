<?php
// Step 1: Start session if it hasn't started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Step 2: Check if user is not logged in, then redirect
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit;
}

include(dirname(__DIR__) . '/backend/display_rating.php');

$carrier_id = $_SESSION['id'];

// Algorithm Step 1: Check if carrier has any booking history
$history_query = "SELECT ld.* FROM shipment s 
                  JOIN loaddetails ld ON s.load_id = ld.id 
                  WHERE s.carrier_id = $carrier_id";
$history_result = $conn->query($history_query);

// Algorithm Step 2: Load suggestion logic
if ($history_result->num_rows > 0) {
    // Carrier has booking history

    // Algorithm Step 2A: Suggest loads based on past destinations
    $sql = "SELECT ld.*, cd.name AS consignor_name, cd.img_srcs AS consignor_img, cd.email AS consignor_email, 
            COALESCE(AVG(r.rating), 0) AS consignor_rating, COUNT(r.id) AS num_reviews
        FROM loaddetails ld
        JOIN consignordetails cd ON ld.consignor_id = cd.id
        LEFT JOIN shipment s ON s.consignor_id = cd.id
        LEFT JOIN reviews r ON s.id = r.shipment_id
        WHERE ld.status = 'notBooked'
        GROUP BY ld.id, cd.name, cd.img_srcs, cd.email";
} else {
    // No booking history, just return all loads
    $sql = "SELECT ld.*, cd.name AS consignor_name, cd.img_srcs AS consignor_img, cd.email AS consignor_email,
                   COALESCE(AVG(r.rating), 0) AS consignor_rating, COUNT(r.id) AS num_reviews
            FROM loaddetails ld
            JOIN consignordetails cd ON ld.consignor_id = cd.id
            LEFT JOIN shipment s ON ld.id = s.load_id
            LEFT JOIN reviews r ON s.id = r.shipment_id
            WHERE ld.status = 'notBooked'
            GROUP BY ld.id, cd.name, cd.img_srcs, cd.email";
}

// Execute the query
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/7b1b8b2fa3.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/maincontentstyle.css">
    <title>Home-Carrier</title>
</head>
<body>
    <div class="main-content">
        <h2>Loads for You</h2>
        <?php
        // Display loads
        if ($result->num_rows > 0) {
            while ($loadrow = $result->fetch_assoc()) {
                echo '
                    <div class="post-container">
                        <div class="user-info">
                            <div class="detail">
                                <img src="' . $loadrow['consignor_img'] . '" alt="">
                                <div>
                                    <p>' . $loadrow['consignor_name'] . '</p>
                                    <small>' . $loadrow['dateofpost'] . '</small>
                                </div>
                            </div>
                            <div style="float: right;">';
                                
                                // Call displayRating function to show consignor rating and reviews
                                echo displayRating($loadrow['consignor_rating'], $loadrow['num_reviews']);

                            echo '</div>
                        </div>
                        <hr>
                        
                        <div class="content-detail">
                            <div class="content-image">
                                <img src="' . $loadrow['img_srcs'] . '" alt="Image" class="post-img">
                            </div>
                            <div class="content-description">
                                <h3>' . $loadrow['name'] . '</h3>
                                <ul>
                                    <li>Origin: ' . $loadrow['origin'] . '</li>
                                    <li>Destination: ' . $loadrow['destination'] . '</li>
                                    <li>Distance: ' . $loadrow['distance'] . ' Km</li>
                                    <li>Weight: ' . $loadrow['weight'] . ' Ton</li>
                                    <li>Description: ' . $loadrow['description'] . '</li>
                                    <li>Price: ' . $loadrow['price'] . '</li>
                                </ul>
                            </div>
                        </div>
                        <hr>
                        <div class="activity-icon booked">
                            <form action="backend/booking.php" method="post">
                                <input type="hidden" name="action" value="book">
                                <input type="hidden" name="load_id" value="' . $loadrow['id'] . '">
                                <input type="hidden" name="carrier_id" value="' . $carrier_id . '">
                                <input type="hidden" name="consignor_id" value="' . $loadrow['consignor_id'] . '">
                                <button type="submit">
                                    <i class="fa-solid fa-handshake-simple"> Book</i>
                                </button>
                            </form>
                        </div>                  
                    </div>';
            }
        }
        ?>
    </div>
</body>
</html>
