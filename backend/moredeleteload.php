<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/maincontentstyle.css">
    <link rel="stylesheet" href="../css/headerfooterstyle.css">
    <link rel="stylesheet" href="../css/sweetAlert.css">
    <link rel="stylesheet" href="../css/submit_review.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <script src="../js/sweetalert.js"></script>
    <title>Load Details</title>
</head>

<body>

    <?php
    // Check if the session has not started, then start the session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    require 'databaseconnection.php'; // Database connection
    
    // Check if the user is not logged in
    if (!isset($_SESSION['email'])) {
        // Redirect the user to the login page or any other authentication page
        header("Location: ../login.php");
        exit;
    }

    // Function to display alerts using SweetAlert
    function showAlert($message, $type = 'error')
    {
        $title = ($type == "success") ? "Success" : (($type == "error") ? "Error" : "");
        echo "<script>
        Swal.fire({
            icon: '$type',
            title: '$title',
            html: '$message',
        }).then((result) => {
            window.location.href = '../home.php';
        });
    </script>";
    }

    // Function to display ratings
    function displayRating($rating, $numReviews)
    {
        // Calculate the number of full, half, and empty stars
        $fullStars = floor($rating); // Full stars
        $halfStars = ($rating - $fullStars) >= 0.5 ? 1 : 0; // Half star
        $emptyStars = 5 - $fullStars - $halfStars; // Empty stars
    
        // Start output
        $output = '<div style="text-align: center;">';

        // Output full stars
        for ($i = 0; $i < $fullStars; $i++) {
            $output .= '<i class="fa-solid fa-star" style="color: gold;"></i>';
        }

        // Output half star
        if ($halfStars) {
            $output .= '<i class="fa-solid fa-star-half-stroke" style="color: gold;"></i>';
        }

        // Output empty stars
        for ($i = 0; $i < $emptyStars; $i++) {
            $output .= '<i class="fa-regular fa-star" style="color: gold;"></i>';
        }

        // Display number of reviews
        $output .= '<small> (' . $numReviews . ' reviews)</small>';
        $output .= '</div>';

        return $output;
    }

    function checkOldReviews($conn, $shipment_id)
    {
        $shipmentId = $shipment_id;
        $reviewerType = $_SESSION['usertype'];

        // Prepare the SQL query
        $sql = 'SELECT id, shipment_id, reviewer_type, rating, review, created_at 
            FROM reviews 
            WHERE shipment_id = ?';

        // Check if the prepared statement is successfully created
        if ($stmt = $conn->prepare($sql)) {
            // Bind the shipment_id parameter
            $stmt->bind_param('i', $shipmentId);

            // Execute the prepared statement
            $stmt->execute();

            // Bind the result columns to variables
            $stmt->bind_result($id, $shipment_id, $reviewer_type, $rating, $review, $created_at);

            $reviews = [];

            // Fetch all results
            while ($stmt->fetch()) {
                // Store each review data in an associative array
                $reviews[] = [
                    'shipment_id' => $shipment_id,
                    'reviewer_type' => $reviewer_type,
                    'rating' => $rating,
                    'review' => $review,
                    'created_at' => $created_at,
                ];
            }

            // Return all reviews found, if any
            return $reviews;
        } else {
            return false;
        }
    }


    if (isset($_POST['action']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $_SESSION['load_id'] = $id;
        $shipment_id = isset($_POST['shipment_id']) ? $_POST['shipment_id'] : ''; // Set shipment_id to an empty string if it is not set
        $action = $_POST['action']; // Assign a value to $action
    
        if ($action == 'delete') {
            // Delete the row
            $sql = "DELETE FROM loaddetails WHERE id = '$id'";
            $sql2 = "DELETE FROM shipment WHERE load_id = '$id'";
            $conn->query($sql2);
            $img_srcs = $_POST['img_srcs'];
            if (file_exists("../" . $img_srcs) && strpos($img_srcs, 'defaultImg') == false) {
                unlink("../" . $img_srcs);
            }
            $conn->query($sql);
            showAlert("Load Deleted Successfully.", "success");
        } elseif ($action == 'edit') {
            // Update Load Details
            header("Location: updateload.php");
        } elseif ($action == 'cancel') { // Cancel Load Details
            try {
                $conn->begin_transaction();

                $sql = "UPDATE loaddetails SET status = 'notBooked' WHERE id = '$id'";
                $sql2 = "DELETE FROM shipment WHERE id = '$shipment_id'";

                $conn->query($sql);
                $conn->query($sql2);

                // Commit the transaction
                $conn->commit();
                showAlert("Load Canceled Successfully.", "success");
                exit;
            } catch (\Throwable $th) {
                $conn->rollback();
                showAlert("ERROR! ' . $th . '", "error");
                exit();
            }
        } elseif ($action == 'deliver') { // Mark Delivered Load Details
            try {
                $sql = "UPDATE loaddetails SET status = 'delivered' WHERE id = '$id'";
                $conn->query($sql);

                // Add this line to update delivered_time in the shipment table
                $sql2 = "UPDATE shipment SET delivered_time = NOW() WHERE load_id = '$id'";
                $conn->query($sql2);

                showAlert("Load Delivered Marked.", "success");
                exit;
            } catch (\Throwable $th) {
                $conn->rollback();
                showAlert("ERROR! ' . $th . '", "error");
                exit();
            }
        } elseif ($action == 'more') {
            // More of the row
            $sql = "SELECT * FROM loaddetails WHERE id = '$id'";
            $result = $conn->query($sql);
            $more = mysqli_fetch_assoc($result);
            $stat = $more['status'];

            // Show by whom
            $sql2 = "SELECT * FROM shipment WHERE load_id = '$id'";
            $result2 = $conn->query($sql2);
            $row = mysqli_fetch_array($result2);
            // Show more
            ?>

            <div class="headdetails">
                <h2>Load Details</h2>
            </div>
            <div class="backBtn">
                <a href="../home.php"><button type="button">Back</button></a>
            </div>
            <div class="more">
                <div class="load-detail-single description-more">
                    <img src="../<?php echo $more['img_srcs']; ?>" alt="Image" class="more-img">
                    <div class="">
                        <h3><?php echo $more['name']; ?></h3>
                        <ul>
                            <li>Origin: <?php echo $more['origin']; ?></li>
                            <li>Destination: <?php echo $more['destination']; ?></li>
                            <li>Distance: <?php echo $more['distance']; ?> Km</li>
                            <li>Weight: <?php echo $more['weight']; ?> Ton</li>
                            <li>Description: <?php echo $more['description']; ?></li>
                            <li>Sceduled by: <?php echo $more['scheduled_time']; ?></li>
                            <?php
                            // Fetch delivered time if the load is delivered
                            if ($stat === 'delivered') {
                                // Query to get delivered_time from shipment table
                                $sql2 = "SELECT delivered_time FROM shipment WHERE load_id = '$id'";
                                $result2 = $conn->query($sql2);
                                if ($result2 && $result2->num_rows > 0) {
                                    $row2 = $result2->fetch_assoc();
                                    $delivered_time = $row2['delivered_time'];

                                    echo "<li>Delivered Time: " . $delivered_time . "</li>";

                                    // Calculate the difference between scheduled_time and delivered_time
                                    $scheduled_time = new DateTime($more['scheduled_time']);
                                    $delivered_time_dt = new DateTime($delivered_time);

                                    $interval = $scheduled_time->diff($delivered_time_dt);
                                    $days = $interval->days;
                                    $hours = $interval->h;

                                    if ($days === 0 && $hours === 0) {
                                        // Delivery is on time (same day)
                                        echo "<li>Delivery was on time.</li>";
                                    } else {
                                        // Determine if delivery was ahead of or late by the scheduled time
                                        $aheadOrLate = ($interval->invert == 1) ? "ahead of" : "late by";
                                        echo "<li>Delivery was $aheadOrLate $days days and $hours hours</li>";
                                    }
                                }
                            }

                            ?>
                        </ul>
                    </div>
                </div>

                <?php
                //what to display
                if ($_SESSION['usertype'] == "carrier") {
                    echo "
                <div class='takenby description-more'>
                <h3 style='text-align:center' important>Load By</h3>";

                    $sql3 = "SELECT consignordetails.id, consignordetails.name, consignordetails.email, consignordetails.address, consignordetails.contact, consignordetails.img_srcs
                FROM consignordetails
                INNER JOIN shipment ON consignordetails.id = shipment.consignor_id    
                WHERE shipment.load_id = '$id'";
                    $result3 = $conn->query($sql3);
                    if ($result3 === false) {
                        // Handle query error
                        echo "Error: " . $conn->error;
                    } else {
                        $rowShip = mysqli_fetch_assoc($result3);
                        if ($rowShip === null) {
                            // No rows returned
                            echo "No booking information available.";
                        } else {
                            // Displaying
                            // Fetch consignor rating
                            $sql_consignor_rating = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS num_reviews
                            FROM reviews
                            WHERE shipment_id IN (
                                SELECT id 
                                FROM shipment 
                                WHERE consignor_id = (SELECT consignor_id FROM loaddetails WHERE id = ?)
                            ) 
                            AND reviews.reviewer_type = 'carrier';
                            ";

                            $stmt = $conn->prepare($sql_consignor_rating);
                            $stmt->bind_param("i", $id);
                            $stmt->execute();
                            $result_consignor = $stmt->get_result();
                            $consignor_rating = $result_consignor->fetch_assoc();
                            $loadrow['consignor_rating'] = $consignor_rating['avg_rating'] ?? 0;
                            $loadrow['num_reviews'] = $consignor_rating['num_reviews'] ?? 0;

                            echo '<ul>';
                            echo '<li style="text-align:center" important><img src="../' . $rowShip["img_srcs"] . '" style="height: 85px; width: auto;"></li>';
                            ?>
                            <li>
                                <?php echo displayRating($loadrow['consignor_rating'], $loadrow['num_reviews']); ?>
                            </li>
                            <?php
                            echo '<li>Name: ' . $rowShip["name"] . '</li>';
                            echo '<li>Email: ' . $rowShip["email"] . '</li>';
                            echo '<li>Address: ' . $rowShip["address"] . '</li>';
                            echo '<li>Contact: ' . $rowShip["contact"] . '</li>';
                            echo '</ul>';
                            if ($stat == 'delivered') {
                                // Fetch old reviews for the current shipment
                                $oldReviews = checkOldReviews($conn, $shipment_id);

                                $alreadyReviewed = false;

                                // Loop through the reviews and check if the current user has already reviewed
                                foreach ($oldReviews as $review) {
                                    if ($review['shipment_id'] == $shipment_id && $review['reviewer_type'] == $_SESSION['usertype']) {
                                        $alreadyReviewed = true;
                                        break;
                                    }
                                }

                                // If no review by current user type, show the review form
                                if (!$alreadyReviewed) {
                                    echo "<p class='delivered-dis'>Delivered</p>";
                                    echo '
                                    <form method="POST" action="" class="review-form">
                                        <div id="star-rating">
                                            <span class="star" data-value="1">&#9733;</span>
                                            <span class="star" data-value="2">&#9733;</span>
                                            <span class="star" data-value="3">&#9733;</span>
                                            <span class="star" data-value="4">&#9733;</span>
                                            <span class="star" data-value="5">&#9733;</span>
                                        </div>
                                        <input type="hidden" name="rating" id="rating" value="0">
                                        <br>
                                        <textarea name="review" placeholder="Write your review here (Optional)..." rows="4" cols="50" required></textarea>
                                        <br>
                                        <input type="hidden" name="shipment_id" value="' . $shipment_id . '">
                                        <button type="submit" name="submitReview" class="review_button">Submit Review</button>
                                    </form>
                                ';
                                } else {
                                    // Display a message if the user has already reviewed
                                    echo "<p class='delivered-dis'>You have already reviewed this shipment.</p>";
                                }
                            }
                        }
                        echo "</div>";
                    }
                    echo "<div class='more-action description-more'>
                <h3>Action</h3>";
                    if ($stat !== 'delivered') {
                        echo "
                    <div class='td-center'>
                        <form action='' method='post' class='cancelBtn' onsubmit=\"confirmCancel(event)\">
                            <input type='hidden' name='action' value='cancel'>
                            <input type='hidden' name='id' value='" . $id . "'>
                            <input type='hidden' name='shipment_id' value='" . $row['id'] . "'> <!--passing shipment id-->
                            <button type='submit'>Cancel</button>
                        </form>
                        <form action='' method='post' class='deliverBtn' onsubmit=\"confirmDeliver(event)\">
                            <input type='hidden' name='action' value='deliver'>
                            <input type='hidden' name='id' value='" . $id . "'>
                            <button type='submit'>Delivered</button>
                        </form>
                    </div>";
                    }
                    echo "</div>";
                } elseif ($_SESSION['usertype'] == "consignor") {
                    echo "
                <div class='takenby description-more'>
                    <h3  style='text-align:center' important>Booked By</h3>";

                    $sql3 = "SELECT carrierdetails.id, carrierdetails.name, carrierdetails.email, carrierdetails.address, carrierdetails.contact, carrierdetails.img_srcs
                FROM carrierdetails
                INNER JOIN shipment ON carrierdetails.id = shipment.carrier_id
                WHERE shipment.load_id = '$id'";

                    $result3 = $conn->query($sql3);
                    if ($result3 === false) {
                        // Handle query error
                        echo "Error: " . $conn->error;
                    } else {
                        $rowShip = mysqli_fetch_assoc($result3);

                        if ($rowShip === null) {
                            // No rows returned
                            echo "No booking information available.";
                        } else {
                            // Displaying
                            // Fetch carrier rating
                            $sql_carrier_rating = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS num_reviews 
                        FROM reviews 
                        WHERE shipment_id IN (SELECT id FROM shipment WHERE carrier_id = (SELECT carrier_id FROM loaddetails WHERE id = ?))
                        AND reviews.reviewer_type = 'consignor'";

                            $stmt = $conn->prepare($sql_carrier_rating);
                            $stmt->bind_param("i", $id);
                            $stmt->execute();
                            $result_carrier = $stmt->get_result();
                            $carrier_rating = $result_carrier->fetch_assoc();
                            $loadrow['carrier_rating'] = $carrier_rating['avg_rating'] ?? 0;
                            $loadrow['num_reviews'] = $carrier_rating['num_reviews'] ?? 0;

                            echo '<ul>';
                            echo '<li style="text-align:center" important><img src="../' . $rowShip["img_srcs"] . '" style="height: 85px; width: auto;"></li>';
                            ?>
                            <li>
                                <?php echo displayRating($loadrow['carrier_rating'], $loadrow['num_reviews']); ?>
                            </li>
                            <?php
                            echo '<li>Name: ' . $rowShip["name"] . '</li>';
                            echo '<li>Email: ' . $rowShip["email"] . '</li>';
                            echo '<li>Address: ' . $rowShip["address"] . '</li>';
                            echo '<li>Contact: ' . $rowShip["contact"] . '</li>';
                            echo '</ul>';
                            echo "<div class='td-center'>";
                            // After delivered no cancel
                            if ($stat !== 'delivered') {
                                echo "<form action='' method='post' class='cancelBtn' onsubmit=\"confirmCancel(event)\">
                            <input type='hidden' name='action' value='cancel'>
                            <input type='hidden' name='id' value='" . $id . "'>
                            <input type='hidden' name='shipment_id' value='" . $row['id'] . "'> <!--passing shipment id-->
                            <button type='submit' name='cancel'>Cancel</button>
                        </form>";
                            } else {
                                // Code to give rating and review for consignor
                                echo '
                                <form method="POST" action="" class="review-form">
                                    <div id="star-rating">
                                        <span class="star" data-value="1">&#9733;</span>
                                        <span class="star" data-value="2">&#9733;</span>
                                        <span class="star" data-value="3">&#9733;</span>
                                        <span class="star" data-value="4">&#9733;</span>
                                        <span class="star" data-value="5">&#9733;</span>
                                    </div>
                                    <input type="hidden" name="rating" id="rating" value="0">
                                    <br>
                                    <textarea name="review" placeholder="Write your review here (Optional) ..." rows="4" cols="50" required></textarea>
                                    <br>                                    
                                    <input type="hidden" name="shipment_id" value="' . $shipment_id . '">
                                    <button type="submit" name="submitReview" class="review_button">Submit Review</button>
                                </form>
                            ';
                            }
                            echo "</div>";
                        }
                    }

                    echo "</div>";
                    echo "<div class='more-action description-more'>
                    <h3>Action</h3>
                    <div class='td-center'>";
                    // After delivered no edit
                    if ($stat !== 'delivered') {
                        echo "
                        <form action='' method='post' class='moreBtn'> <!--class more for css-->
                            <input type='hidden' name='action' value='edit'>
                            <input type='hidden' name='id' value='" . $id . "'>
                            <button type='submit'>Edit</button>
                        </form>";
                    }
                    echo "                                        
                        <form action='' method='post' class='deleteBtn' onsubmit=\"confirmDelete(event)\">
                            <input type='hidden' name='action' value='delete'>
                            <input type='hidden' name='id' value='" . $id . "'>
                            <input type='hidden' name='img_srcs' value='" . htmlspecialchars($more['img_srcs'], ENT_QUOTES, 'UTF-8') . "'>
                            <button type='submit'>Delete</button>
                        </form>
                    </div>
                </div>";
                }
                ?>
            </div>
            <?php
        }
    }


    if (isset($_POST['submitReview'])) {
        $shipmentId = $_POST['shipment_id'];
        $rating = $_POST['rating'];
        $review = $_POST['review'];

        // Validate input
        if (empty($shipmentId) || empty($rating) || empty($review)) {
            showAlert("All fields are required.", "error");
        } else {
            // Prepare SQL statement
            $stmt = $conn->prepare("INSERT INTO reviews (shipment_id, reviewer_type, rating, review) VALUES (?, ?, ?, ?)");

            // Bind parameters
            $stmt->bind_param("isss", $shipmentId, $_SESSION['usertype'], $rating, $review);

            // Execute SQL statement
            if ($stmt->execute()) {
                // Show success alert
                showAlert("Your review has been submitted.", "success");
            } else {
                // Show error alert
                showAlert("There was an error submitting your review.", "error");
            }

            // Close SQL statement
            $stmt->close();
        }
    }


    include '../layout/footer.php';
    ?>



    <script src="../js/confirmationSA.js"></script>
    <script src="../js/review_stars.js"></script>
</body>

</html>