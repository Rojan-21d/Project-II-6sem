<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'databaseconnection.php'; // Database connection
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['id'];
$user_type = $_SESSION['usertype'];

if ($user_type == 'carrier') {
    // Carrier: fetch details from carrierdetails table
    $sql_user = "SELECT * FROM carrierdetails WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
} elseif ($user_type == 'consignor') {
    // Consignor: fetch details from consignordetails table
    $sql_user = "SELECT * FROM consignordetails WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
}

$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();

// Display user details
echo "<h2>User Details</h2>";
echo "<ul>";
echo "<li>Type: " . htmlspecialchars($user_type) . "</li>";
echo "<li>Name: " . htmlspecialchars($user['name']) . "</li>";
echo "<li>Email: " . htmlspecialchars($user['email']) . "</li>";
echo "<li>Contact: " . htmlspecialchars($user['contact']) . "</li>";
echo "</ul>";

echo "<h3>Reviews</h3>";

// Query to fetch reviews and relevant shipment details
if ($user_type == 'carrier') {
    // Carrier viewing consignor's reviews
    $sql_reviews = "
        SELECT r.id, r.rating, r.review, r.created_at, s.load_id, s.delivered_time, co.name AS consignor_name
        FROM reviews r
        JOIN shipment s ON r.shipment_id = s.id
        JOIN consignordetails co ON s.consignor_id = co.id
        WHERE r.reviewer_type = 'carrier' AND s.carrier_id = ?";
    $stmt_reviews = $conn->prepare($sql_reviews);
    $stmt_reviews->bind_param("i", $user_id);
} else {
    // Consignor viewing carrier's reviews
    $sql_reviews = "
        SELECT r.id, r.rating, r.review, r.created_at, s.load_id, s.delivered_time, ca.name AS carrier_name
        FROM reviews r
        JOIN shipment s ON r.shipment_id = s.id
        JOIN carrierdetails ca ON s.carrier_id = ca.id
        WHERE r.reviewer_type = 'consignor' AND s.consignor_id = ?";
    $stmt_reviews = $conn->prepare($sql_reviews);
    $stmt_reviews->bind_param("i", $user_id);
}

$stmt_reviews->execute();
$reviews_result = $stmt_reviews->get_result();

if ($reviews_result->num_rows > 0) {
    echo "<ul>";
    while ($review = $reviews_result->fetch_assoc()) {
        echo "<li>";
        echo "<strong>Rating: " . $review['rating'] . "/5</strong><br>";
        echo "Review: " . htmlspecialchars($review['review']) . "<br>";
        echo "Date: " . date('d-m-Y', strtotime($review['created_at'])) . "<br>";

        // Display related shipment details
        echo "<strong>Shipment Details:</strong><br>";
        echo "Load ID: " . htmlspecialchars($review['load_id']) . "<br>";
        echo "Delivered Time: " . htmlspecialchars($review['delivered_time']) . "<br>";

        // Display the name of the other party (Consignor or Carrier)
        if ($user_type == 'carrier') {
            echo "Consignor: " . htmlspecialchars($review['consignor_name']) . "<br>";
        } else {
            echo "Carrier: " . htmlspecialchars($review['carrier_name']) . "<br>";
        }

        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No reviews found.</p>";
}

// Close connections
$stmt_user->close();
$stmt_reviews->close();
$conn->close();
?>
