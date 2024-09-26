
// Client-side validation function with SweetAlert integration
document.querySelector('form').addEventListener('submit', function (event) {
    if (!validateForm()) {
        event.preventDefault();
    }
});
function validateForm() {
    var errors = [];
    var name = document.getElementById("name").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var phone = document.getElementById("phone").value;
    
    var reName = /^[A-Z][a-zA-Z]*(?: [A-Z][a-zA-Z]*)*$/;
    if (!reName.test(name)) {
        errors.push("Name must be like 'Rojan Dumaru'");
    }
    
    if (name === "") {
        errors.push("Name is required.");
    }
    
    if (email === "") {
        errors.push("Email is required.");
    } else if (!validateEmail(email)) {
        errors.push("Invalid email format.");
    }
    
    if (password === "") {
        errors.push("Password is required.");
    } else if (password.length < 8 || password.length > 24) {
        errors.push("Password must be between 8 and 24 characters.");
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
            title: 'Sign Up Error',
            html: errorMessage,
            showCloseButton: true,
        });            
        return false;
    }
    
    return true;
}

// Email validation function
function validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}
