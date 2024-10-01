document.addEventListener('DOMContentLoaded', function () {
    // Get today's date in YYYY-MM-DDTHH:MM format (for datetime-local input)
    const now = new Date();
    const year = now.getFullYear();
    const month = ('0' + (now.getMonth() + 1)).slice(-2); // Month in MM format
    const day = ('0' + now.getDate()).slice(-2); // Day in DD format
    const hours = ('0' + now.getHours()).slice(-2); // Hours in HH format
    const minutes = ('0' + now.getMinutes()).slice(-2); // Minutes in MM format

    // Set the min attribute to the current date and time
    const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
    document.getElementById('scheduled_time').setAttribute('min', minDateTime);
});