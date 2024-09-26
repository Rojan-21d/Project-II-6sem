// imgPreview.js
function previewImage(event) {
    var reader = new FileReader();
    var imagePreview = document.getElementById('PicPreview');
    reader.onload = function() {
        imagePreview.src = reader.result;
    }

    const fileInput = document.getElementById('pic');
    const selectedFile = fileInput.files[0];
    if (selectedFile) {
        const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
        if (allowedExtensions.exec(selectedFile.name)) {
            reader.readAsDataURL(selectedFile);
        }
    } else {
        imagePreview.src = ''; // Clear the preview if no file selected
    }
}