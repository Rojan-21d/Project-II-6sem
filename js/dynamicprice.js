$(document).ready(function() {
    function updatePrice() {
        const distance = $('#distance').val();
        const weight = $('#weight').val();
        const scheduledTime = $('#scheduled_time').val();

        if (distance && weight) {
            $.ajax({
                url: '../backend/calculate_price.php',
                type: 'POST',
                data: {
                    distance: distance,
                    weight: weight,
                    scheduled_time: scheduledTime,
                    calculatePrice: true
                },
                dataType: 'json',
                success: function(response) {
                    if (response.price) {
                        $('#calculated_price').text(Number(response.price).toFixed(2));
                    } else {
                        $('#calculated_price').text('0');
                    }
                },
                error: function() {
                    $('#calculated_price').text('0');
                }
            });
        } else {
            $('#calculated_price').text('0');
        }
    }

    $('#distance, #weight, #scheduled_time').on('input change', updatePrice);
});
