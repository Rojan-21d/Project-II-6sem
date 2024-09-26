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

// Carrier ID from session
$carrier_id = $_SESSION['id'];

// Connect to the database (ensure $conn is defined in your script)

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
            LEFT JOIN reviews r ON cd.id = r.consignor_id
            WHERE ld.status = 'notBooked' AND 
                ld.destination IN (SELECT destination FROM shipment WHERE carrier_id = $carrier_id)
            GROUP BY ld.id, cd.name, cd.img_srcs, cd.email";

} else {
    // Carrier has no booking history

    // Step 3: Get user's current location if available
    if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
        $_SESSION['latitude'] = $_POST['latitude'];
        $_SESSION['longitude'] = $_POST['longitude'];
    } else {
        $_SESSION['latitude'] = null;
        $_SESSION['longitude'] = null;
    }

    // Set latitude and longitude for calculation
    $latitude = $_SESSION['latitude'];
    $longitude = $_SESSION['longitude'];

    // Algorithm Step 2B: Suggest loads based on location proximity (if location is available)
    if ($latitude !== null && $longitude !== null) {
        $sql = "SELECT ld.*, cd.name AS consignor_name, cd.img_srcs AS consignor_img, cd.email AS consignor_email, 
                    COALESCE(AVG(r.rating), 0) AS consignor_rating, COUNT(r.id) AS num_reviews,
                    (6371 * acos(cos(radians($latitude)) * cos(radians(ld.origin_latitude)) 
                    * cos(radians(ld.origin_longitude) - radians($longitude)) 
                    + sin(radians($latitude)) * sin(radians(ld.origin_latitude)))) AS distance 
                FROM loaddetails ld
                JOIN consignordetails cd ON ld.consignor_id = cd.id
                LEFT JOIN reviews r ON cd.id = r.consignor_id
                WHERE ld.status = 'notBooked'
                HAVING distance < 50
                GROUP BY ld.id, cd.name, cd.img_srcs, cd.email
                ORDER BY distance ASC";
    } else {
        // No location available, just return all loads
        $sql = "SELECT ld.*, cd.name AS consignor_name, cd.img_srcs AS consignor_img, cd.email AS consignor_email,
                       COALESCE(AVG(r.rating), 0) AS consignor_rating, COUNT(r.id) AS num_reviews
                FROM loaddetails ld
                JOIN consignordetails cd ON ld.consignor_id = cd.id
                LEFT JOIN reviews r ON cd.id = r.consignor_id
                WHERE ld.status = 'notBooked'
                GROUP BY ld.id, cd.name, cd.img_srcs, cd.email";
    }
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
                                
                                // Displaying 5-star rating and the number of reviews for consignor
                                $fullStars = floor($loadrow['consignor_rating']); // Full stars
                                $halfStars = ($loadrow['consignor_rating'] - $fullStars) >= 0.5 ? 1 : 0; // Half star
                                $emptyStars = 5 - $fullStars - $halfStars; // Empty stars
                                $numReviews = $loadrow['num_reviews']; // Number of reviews

                                // Output full stars
                                for ($i = 0; $i < $fullStars; $i++) {
                                    echo '<i class="fa-solid fa-star" style="color: gold;"></i>';
                                }

                                // Output half star
                                if ($halfStars) {
                                    echo '<i class="fa-solid fa-star-half-stroke" style="color: gold;"></i>';
                                }

                                // Output empty stars
                                for ($i = 0; $i < $emptyStars; $i++) {
                                    echo '<i class="fa-regular fa-star" style="color: gold;"></i>';
                                }

                                // Display number of reviews
                                echo '<small> (' . $numReviews . ' reviews)</small>';
                                
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
                                </ul>
                            </div>
                        </div>
                        <hr>
                        <div class="activity-icon booked">
                            <form action="backend/booking.php" method="post">
                                <!-- Pass load ID, carrier ID, and consignor ID for booking -->
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
