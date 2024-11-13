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

$carrier_id = $_SESSION['id'];

$sql = "SELECT ld.*, cd.name AS consignor_name, cd.img_srcs AS consignor_img, cd.email AS consignor_email
    FROM loaddetails ld
    JOIN consignordetails cd ON ld.consignor_id = cd.id
    LEFT JOIN shipment s ON ld.id = s.load_id
    WHERE ld.status = 'notBooked'
    GROUP BY ld.id, cd.name, cd.img_srcs, cd.email;
";

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
