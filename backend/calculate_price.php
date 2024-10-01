<?php

require 'databaseconnection.php'; 

function calculateDynamicPrice($db, $distance, $weight, $scheduled_time) {
    // Fetch pricing configuration from the database
    $config = [];
    $result = $db->query("SELECT config_name, config_value FROM pricing_config");
    
    if ($result === false) {
        die("Error fetching pricing configuration: " . $db->error);
    }

    while ($row = $result->fetch_assoc()) {
        $config[$row['config_name']] = $row['config_value'];
    }

    // Fetch weight class pricing from the database
    $basePrice = 0; // Defining basePrice variable
    $result = $db->query("SELECT base_price_min, base_price_max FROM weight_class_pricing WHERE min_weight <= $weight AND max_weight >= $weight");
    
    if ($result === false) {
        die("Error fetching weight class pricing: " . $db->error);
    }
    
    if ($row = $result->fetch_assoc()) {
        // Calculate average base price for the weight class
        $basePrice = ($row['base_price_min'] + $row['base_price_max']) / 2;
    } else {
        die("Error: Weight class not found.");
    }

    $distanceFactor = $config['distance_factor'];

    // Calculate urgency factor based on scheduled time
    $currentTimestamp = time();
    $scheduledTimestamp = strtotime($scheduled_time);
    $hoursRemaining = ($scheduledTimestamp - $currentTimestamp) / 3600; // Convert seconds to hours
    
    $urgencyFactor = 0;
    if ($hoursRemaining > 0) {
        // Calculate urgency increase based on remaining hours
        // $averageKmPerDay = 400;
        $averageKmPerDay = $config['average_km_per_day'];
        $urgencyFactor = max(0, $config['urgency_factor'] * ($distance / $averageKmPerDay * 24 - $hoursRemaining));
    }

    // Calculate final price in Nepali Rupees
    $finalPrice = ($basePrice + ($distanceFactor * $distance) + $urgencyFactor);

    return $finalPrice; // This will return the price in NPR
}

// Get the parameters from the AJAX request
$distance = isset($_POST['distance']) ? (float)$_POST['distance'] : 0;
$weight = isset($_POST['weight']) ? (float)$_POST['weight'] : 0;
$scheduled_time = isset($_POST['scheduled_time']) ? $_POST['scheduled_time'] : date('Y-m-d H:i:s');

// Calculate the price
$price = calculateDynamicPrice($conn, $distance, $weight, $scheduled_time);

// Return the price as JSON
echo json_encode(['price' => $price]);
?>
