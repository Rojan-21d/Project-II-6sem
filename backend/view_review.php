<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consignor and Reviews</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional: Font Awesome for star icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .rounded-circle {
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        require 'databaseconnection.php'; // Database connection
        if (!isset($_SESSION['email'])) {
            header("Location: ../login.php");
            exit;
        }

        include 'display_rating.php';

        $user_id = $_SESSION['id'];
        $user_type = $_SESSION['usertype'];

        // Set user details query based on user type
        if ($user_type == 'carrier') {
            if (isset($_GET['consignor_id'])) {
                $consignor_id = $_GET['consignor_id'];
                $sql_user = "SELECT * FROM consignordetails WHERE id = ?";
                $stmt_user = $conn->prepare($sql_user);
                $stmt_user->bind_param("i", $consignor_id);
            } else {
                echo "No consignor selected!";
                exit;
            }
        } elseif ($user_type == 'consignor') {
            if (isset($_GET['carrier_id'])) {
                $carrier_id = $_GET['carrier_id'];
                $sql_user = "SELECT * FROM carrierdetails WHERE id = ?";
                $stmt_user = $conn->prepare($sql_user);
                $stmt_user->bind_param("i", $carrier_id);
            } else {
                echo "No carrier selected!";
                exit;
            }
        }

        $stmt_user->execute();
        $user_result = $stmt_user->get_result();
        $user = $user_result->fetch_assoc();

        if ($user) {
            echo "<div class='d-flex justify-content-between align-items-center mb-4'>
                    <h2 class='text-primary'>Review in Detail</h2>
                    <a href='../home.php' class='btn btn-secondary'>Back</a>
                </div>";
            echo "<div class='card mb-5 shadow-sm'>";
            echo "<div class='card-body'>";
            echo "<img src='path/to/default_user_image.jpg' alt='User Photo' class='img-thumbnail rounded-circle mb-3' style='width: 100px; height: 100px;'>";
            echo "<h5 class='card-title'>" . htmlspecialchars($user['name']) . "</h5>";
            echo "<ul class='list-group list-group-flush'>";
            echo "<li class='list-group-item'><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</li>";
            echo "<li class='list-group-item'><strong>Contact:</strong> " . htmlspecialchars($user['contact']) . "</li>";
            echo "</ul>";
            echo "</div></div>";

            // Set reviews query based on user type
            if ($user_type == 'carrier') {
                $sql_reviews = "
                SELECT r.id, r.rating, r.review, r.created_at, s.load_id, s.delivered_time, ca.name AS carrier_name
                FROM reviews r
                JOIN shipment s ON r.shipment_id = s.id
                JOIN carrierdetails ca ON s.carrier_id = ca.id
                WHERE r.reviewer_type = 'carrier' AND s.consignor_id = ?";
                $stmt_reviews = $conn->prepare($sql_reviews);
                $stmt_reviews->bind_param("i", $consignor_id);
            } elseif ($user_type == 'consignor') {
                $sql_reviews = "
                SELECT r.id, r.rating, r.review, r.created_at, s.load_id, s.delivered_time, co.name AS consignor_name
                FROM reviews r
                JOIN shipment s ON r.shipment_id = s.id
                JOIN consignordetails co ON s.consignor_id = co.id
                WHERE r.reviewer_type = 'consignor' AND s.carrier_id = ?";
                $stmt_reviews = $conn->prepare($sql_reviews);
                $stmt_reviews->bind_param("i", $carrier_id);
            }

            $stmt_reviews->execute();
            $reviews_result = $stmt_reviews->get_result();

            if ($reviews_result->num_rows > 0) {
                echo "<div class='row'>";
                while ($review = $reviews_result->fetch_assoc()) {
                    echo "<div class='col-md-9 mb-4'>";
                    echo "<div class='card shadow-sm'>";
                    echo "<div class='card-body'>";

                    // Display stars with margin from the function
                    echo "<div class='mb-3'>" . displayRating($review['rating'], 1) . "</div>";

                    echo "<p><strong>Review:</strong> " . htmlspecialchars($review['review']) . "</p>";
                    echo "<p><small><strong>Date:</strong> " . date('d-m-Y', strtotime($review['created_at'])) . "</small></p>";

                    // Display related shipment details
                    echo "<hr><p><strong>Shipment Details:</strong><br>";
                    echo "Load ID: " . htmlspecialchars($review['load_id']) . "<br>";
                    echo "Delivered Time: " . htmlspecialchars($review['delivered_time']) . "</p>";

                    // Display the name of the consignor/carrier who wrote the review with a photo placeholder
                    echo "<div class='d-flex align-items-center mt-3'>";
                    echo "<img src='path/to/default_user_image.jpg' alt='Reviewer Photo' class='img-thumbnail rounded-circle mr-3' style='width: 50px; height: 50px;'>";
                    echo "<p class='mb-0'><strong>Reviewer:</strong> " . htmlspecialchars($review[$user_type == 'carrier' ? 'carrier_name' : 'consignor_name']) . "</p>";
                    echo "</div>";

                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
                echo "</div>";
            } else {
                echo "<p class='text-center text-muted'>No reviews found.</p>";
            }
        }

        $stmt_user->close();
        $stmt_reviews->close();
        $conn->close();
        ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
