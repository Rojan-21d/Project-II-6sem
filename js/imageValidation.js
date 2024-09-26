//File type validation

document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('profile_pic');

    fileInput.addEventListener('change', () => {
        const selectedFile = fileInput.files[0];
        if (selectedFile) {
            const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
            if (!allowedExtensions.exec(selectedFile.name)) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Format Error',
                    text: 'Invalid file format. Please select a JPG, JPEG, or PNG image.',
                    showCloseButton: true,
                });
                fileInput.value = ''; // Clear the input
            }
        }
    });
});