<?php 
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
?>