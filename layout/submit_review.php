<?php
session_start();
include 'db_connection.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get values from the form
    $user_id = $_POST['user_id'];
    $consignor_id = $_POST['consignor_id'];
    $carrier_id = $_POST['carrier_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    // Insert the review into the database
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, consignor_id, carrier_id, rating, review) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $user_id, $consignor_id, $carrier_id, $rating, $review);

    if ($stmt->execute()) {
        // Redirect or show a success message
        echo "Review submitted successfully!";
        header("Location: previous_page.php"); // Change to your redirect page
    } else {
        // Handle errors
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
