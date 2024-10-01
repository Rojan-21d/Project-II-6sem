<?php
require 'databaseconnection.php'; // Include the database connection file

function calculateDynamicPrice($db, $distance, $weight, $scheduledTime, $currentTime, $averageKmPerDay) {
    // Fetch pricing configuration from the database
    $config = [];
    $result = $db->query("SELECT config_name, config_value FROM pricing_config");
    
    if ($result === false) {
        // Handle database query error
        die("Error fetching pricing configuration: " . $db->error);
    }

    while ($row = $result->fetch_assoc()) {
        $config[$row['config_name']] = $row['config_value'];
    }

    // Use the configuration values in the pricing algorithm
    $basePricePerKm = $config['base_price_per_km']; // New configuration for base price per kilometer
    $distanceFactor = $config['distance_factor_per_km']; // Factor to multiply per kilometer

    // Calculate base price based on the distance provided by the user
    $basePrice = $distance * $basePricePerKm;

    // Calculate distance-based factor
    $distancePrice = $distance * $distanceFactor;

    // Calculate urgency factor based on the scheduled time and average km drivers cover per day
    $urgencyFactor = 0; // Default to no urgency
    $hoursDifference = (strtotime($scheduledTime) - strtotime($currentTime)) / 3600; // Difference in hours

    // Calculate the required time to cover the distance based on the average km per day
    $daysRequired = $distance / $averageKmPerDay;
    $hoursRequired = $daysRequired * 24;

    // If the scheduled time is less than the required time, urgency factor increases
    if ($hoursDifference <= $hoursRequired) {
        $urgencyFactor = ($hoursRequired - $hoursDifference) * $config['urgency_factor_multiplier']; // Increase urgency as time decreases
    }

    // Weight-based price increment
    $weightFactor = ($weight > 5000) ? $weight * 0.2 : $weight * 0.1; // Higher cost for heavier loads

    // Calculate final price (no supply-demand factor in this case)
    $finalPrice = $basePrice + $distancePrice + $weightFactor + $urgencyFactor;

    return $finalPrice;
}

// Example usage (assuming $db is a valid database connection)
// $distance = 200; // Distance in kilometers
// $weight = 6000; // Weight in kilograms
// $scheduledTime = "2024-10-05 15:00:00"; // Scheduled time for the load
// $currentTime = date('Y-m-d H:i:s'); // Current time
// $averageKmPerDay = 500; // Average kilometers a driver can cover per day

// $price = calculateDynamicPrice($db, $distance, $weight, $scheduledTime, $currentTime, $averageKmPerDay);
// echo "Final Price: $" . $price;

?>
