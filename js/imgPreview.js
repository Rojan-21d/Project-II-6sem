// imgPreview.js
function previewImage(event) {
    var reader = new FileReader();
    var imagePreview = document.getElementById('PicPreview');
    const fileInput = event.target; // Get the input element from the event
    const selectedFile = fileInput.files[0]; // Get the selected file

    if (selectedFile) {
        const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
        if (allowedExtensions.exec(selectedFile.name)) {
            // Only preview if the file is an image (JPG, JPEG, PNG)
            reader.onload = function() {
                imagePreview.src = reader.result; // Set the image source to the file's data URL
            };
            reader.readAsDataURL(selectedFile); // Read the image file as a data URL
        } else {
            // If the selected file type is not valid, display an error or clear the preview
            alert('Invalid file format. Please select a JPG, JPEG, or PNG image.');
            imagePreview.src = ''; // Clear the preview
            fileInput.value = ''; // Clear the selected file input
        }
    } else {
        // If no file is selected, clear the image preview
        imagePreview.src = '';
    }
}
