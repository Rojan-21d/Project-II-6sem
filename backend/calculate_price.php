<?php

require 'databaseconnection.php'; 

function calculateDynamicPrice($db, $distance, $weight, $scheduled_time) {
    // Initialize pricing configuration array
    $config = [];
    // Fetch pricing configuration from the database
    $result = $db->query("SELECT config_name, config_value FROM pricing_config");
    if ($result === false) {
        showAlert("Error fetching pricing configuration: " . $db->error, 'error');
        return null;
    }

    while ($row = $result->fetch_assoc()) {
        $config[$row['config_name']] = $row['config_value'];
    }

    // Get base price for the weight class
    $result = $db->query("SELECT base_price_min, base_price_max FROM weight_class_pricing WHERE min_weight <= $weight AND max_weight >= $weight");
    if ($result === false) {
        showAlert("Error fetching weight class pricing: " . $db->error, 'error');
        return null;
    }
    
    if ($row = $result->fetch_assoc()) {
        $basePrice = ($row['base_price_min'] + $row['base_price_max']) / 2;
    } else {
        showAlert("Error: Weight class not found.", 'error');
        return null;
    }

    // Retrieve the distance factor and urgency factor from the configuration
    $distanceFactor = isset($config['distance_factor']) ? (float)$config['distance_factor'] : 1;
    $urgencyFactorRate = isset($config['urgency_factor']) ? (float)$config['urgency_factor'] : 0;
    $averageKmPerDaySingle = isset($config['average_km_per_day_single']) ? (float)$config['average_km_per_day_single'] : 400; // For 1 driver
    $averageKmPerDayTriple = isset($config['average_km_per_day_triple']) ? (float)$config['average_km_per_day_triple'] : 1200; // For 3 drivers

    // Calculate urgency factor based on the scheduled time
    $currentTimestamp = time();
    $scheduledTimestamp = strtotime($scheduled_time);
    $hoursRemaining = ($scheduledTimestamp - $currentTimestamp) / 3600; // hours remaining

    // Calculate expected travel hours for 400 km/day and 1200 km/day
    $expectedTravelHours400 = ($distance / $averageKmPerDaySingle) * 24;
    $expectedTravelHours1200 = ($distance / $averageKmPerDayTriple) * 24;

    // Check if the scheduled time is too early for even 1200 km/day
    if ($hoursRemaining < $expectedTravelHours1200) {
        showAlert("Scheduled time too early. Please select a later time.", 'warning');
        return null;
    }

    $urgencyFactor = 0;
    if ($hoursRemaining < $expectedTravelHours400 && $hoursRemaining > $expectedTravelHours1200) {
        // Apply urgency factor only if time is shorter than 400 km/day expectation, but not below 1200 km/day expectation
        $urgencyFactor = max(0, $urgencyFactorRate * (($expectedTravelHours400 - $hoursRemaining) / 24));
    }

    // Calculate final price by combining base price, distance factor, and urgency factor
    $finalPrice = $basePrice + ($distanceFactor * $distance) + $urgencyFactor;
    return round($finalPrice, 2); // Rounded to 2 decimal places
}


// Get the parameters from the AJAX request
$distance = isset($_POST['distance']) ? (float)$_POST['distance'] : 0;
$weight = isset($_POST['weight']) ? (float)$_POST['weight'] : 0;
$scheduled_time = isset($_POST['scheduled_time']) ? $_POST['scheduled_time'] : date('Y-m-d H:i:s');

$price = calculateDynamicPrice($conn, $distance, $weight, $scheduled_time);

if ($price === null) {
    // If there was an error or warning in calculation, exit without returning a price
    exit;
}

// Return the price as JSON
if (json_encode(['price' => $price]) !== json_encode(['price' => 250])) {
    // If it's not equal, return the JSON response
    echo json_encode(['price' => $price]);
}

function showAlert($message, $type = 'error') {
    $title = ($type == "success") ? "Success" : (($type == "warning") ? "Warning" : "Error");
    echo "<script>
    Swal.fire({
        icon: '$type',
        title: '$title',
        html: '$message',
    }).then((result) => {
        window.location.href = '../home.php'; // Redirect after alert
    });
    </script>";
}
?>
