$(document).ready(function() {
    // Function to calculate the minimum date for delivery
    function calculateMinimumDate() {
        var distance = parseFloat($('#distance').val()); // Get the distance from input
        var averageKmPerDriverPerDay = 400;
        var drivers = 3;
        var hoursInDay = 24;

        if (isNaN(distance) || distance <= 0) {
            return;
        }

        var totalKmPerDay = averageKmPerDriverPerDay * drivers;
        var daysRequired = Math.ceil(distance / totalKmPerDay);
        var currentDate = new Date();
        currentDate.setDate(currentDate.getDate() + daysRequired); // Add days to the current date
        // Format the date in YYYY-MM-DDTHH:MM format (required for datetime-local input)
        var minimumDate = currentDate.toISOString().slice(0, 16);

        // Set the minimum date on the scheduled time input
        $('#scheduled_time').attr('min', minimumDate);
    }

    // Recalculate the minimum date when the distance field changes
    $('#distance').on('input', function() {
        calculateMinimumDate();
    });

    calculateMinimumDate();
});
