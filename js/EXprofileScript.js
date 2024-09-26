function enableEdit(field) {
    document.getElementById(field).readOnly = false;
}

function openFileInput() {
    document.getElementById('profile_pic').click();
}
// Preview the selected image before uploading
document.getElementById('profile_pic').addEventListener('change', function () {
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function (event) {
            document.getElementById('profilePicPreview').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('form').addEventListener('submit', function (event) {
        if (!validateForm()) {
            event.preventDefault();
        }
    });
    
    // Client-side validation function with SweetAlert integration
    function validateForm() {
        var errors = [];
        var name = document.getElementById("name").value;
        var email = document.getElementById("email").value;
        var password = document.getElementById("password").value;
        var phone = document.getElementById("contact").value; // Corrected ID
        
        if (name === "") {
            errors.push("Name is required.");
        }
        
        if (email === "") {
            errors.push("Email is required.");
        } else if (!validateEmail(email)) {
            errors.push("Invalid email format.");
        }
        
        
        if (password !== "") { // Check if password is provided
            if (password.length < 8 || password.length > 24) {
                errors.push("Password must be between 8 and 24 characters.");
            }
        }
        
        if (phone === "") {
            errors.push("Phone number is required.");
        } else if (phone.length !== 10) {
            errors.push("Phone number must be 10 digits.");
        }
        
        // Display errors using SweetAlert with bullet points
        if (errors.length > 0) {
            var errorMessage = `<div class="error-list">${errors.map(error => `• ${error}`).join("<br>")}</div>`;
            Swal.fire({
                icon: 'error',
                title: 'Update Error',
                html: errorMessage,
                showCloseButton: true,
            });
            
            return false; // Prevent form submission
        }
        
        return true; // Allow form submission
    }
    
    function validateEmail(email) {
        var re = /\S+@\S+\.\S+/;
        return re.test(email);
    }
});