// Client-side validation function
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
    var phone = document.getElementById("contact").value;

    var reName = /^[A-Z][a-zA-Z]*(?: [A-Z][a-zA-Z]*)*$/;
    if (!reName.test(name)) {
        errors.push("Name must be only alphabetical and like Rojan Dumaru");
    }
    
    if (name.trim() === "") {
        errors.push("Name is required.");
    }

    if (email.trim() === "") {
        errors.push("Email is required.");
    } else if (!validateEmail(email)) {
        errors.push("Invalid email format.");
    }

    if (password.trim() !== "") {
        if (password.length < 8 || password.length > 24) {
            errors.push("Password must be between 8 and 24 characters.");
        }
    }
    
    if (phone === "") {
        errors.push("Phone number is required.");
    } else if (isNaN(phone)) {
        errors.push("Phone number must be numeric.");
        
    } else if (phone.length !== 10) {
        errors.push("Phone number must be 10 digits.");
    }

    if (errors.length > 0) {
        var errorMessage = errors.join("<br>");
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

function validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

// Function to enable editing for all input fields at once
function enableEditAll() {
    var inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
    inputs.forEach(function(input) {
        input.readOnly = false;
    });
}

function openFileInput() {
    document.getElementById('pic').click();
}
