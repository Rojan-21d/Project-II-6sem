<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>   -->
    <script src="js/sweetalert.js"></script>
    <script src="js/imageValidation.js"></script>
    <!-- <script src="https://kit.fontawesome.com/7b1b8b2fa3.js" crossorigin="anonymous"></script> -->
    <script src="js/fontAwesome.js"></script>
    <link rel="stylesheet" href="css/registration.css">
    <link rel="stylesheet" href="css/sweetAlert.css">
    <title>Gantabya - Sign up</title>
</head>
<body>
    
<?php
$errors = array(); // A single array to store all validation errors

// ... Database connection ...
require 'backend/databaseconnection.php';

if (isset($_POST['signupBtn'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $userselects = $_POST['userselects'];
    $table = ($userselects === "carrier") ? "carrierdetails" : "consignordetails";

    // Validate form inputs
    if (empty($name) || empty($email) || empty($contact) || empty($address) || empty($password)) {
        $errors[] = "PHP All fields are required";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "PHP Invalid email format";
    }

    $reNameRegEx = '/^[A-Z][a-zA-Z]*(?: [A-Z][a-zA-Z]*)*$/';
    if(!preg_match($reNameRegEx, $name)){
        $errors[] = "PHP Name must be only alphabetical and like Rojan Dumaru";
    }

    if (strlen($password) < 8 || strlen($password) > 24) {
        $errors[] = "PHP Password must be between 8 and 24 characters";
    }

    if (!is_numeric($contact)){
        $errors[] = "Contact must be a numeric value.";
        if (strlen($contact) !== 10) {
            $errors[] = "PHP Contact Number Length must be 10";
        }
    }   
    // Uniqye Key Email Validation 
    $sql_check_mail = "SELECT * FROM $table WHERE email = '$email'";
    $result_check_mail = $conn->query($sql_check_mail);
    if ($result_check_mail->num_rows > 0){
        $errors[] = "Email Already Registered";
    }

    // ... Image upload validation ...
    if (!empty($_FILES['profile_pic']['name'])) {
        $allowed_formats = array('jpg', 'jpeg', 'png');
        $upload_directory = 'img/profileUploads/';
        $img_name = $_FILES['profile_pic']['name'];
        $img_extension = pathinfo($img_name, PATHINFO_EXTENSION);

        // Validate the file extension
        if (!in_array(strtolower($img_extension), $allowed_formats)) {
            $errors[] = "Only JPG, JPEG, and PNG images are allowed.";
        } else {
            $uploaded_file_path = $upload_directory . $img_name;
            if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploaded_file_path)) {
                $errors[] = "Error uploading the image.";
            }
        }
    } else {
        $uploaded_file_path = 'img/defaultImg/user-regular.png';
    }

    if (empty($errors)) {

        // Prepare and execute the SQL query
        $sql = "INSERT INTO $table (name, img_srcs, email, contact, address, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $errors[] = "Error in database connection.";
        } else {
            // Hashing password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Bind parameters and execute
            $stmt->bind_param("ssssss", $name, $uploaded_file_path, $email, $contact, $address, $hashedPassword);
            if ($stmt->execute()) {
                header("Location: signup.php?success=1");
                exit;
            } else {
                $errors[] = "An error occurred while processing your request. Please try again later.";
            }
        }
    }
}

// Display errors using SweetAlert
if (!empty($errors)) {
    $errorMessages = join("\n", $errors);
    echo '.<script>
    document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        icon: "error",
        title: "Sign Up Errors",
        html: "' . $errorMessages . '",
        showCloseButton: true,
    });
});

    </script>';
}
?>



    <div class="container">
        <div class="form-box">
            <div class="topic">
                <h1>Sign Up</h1>
            </div>
            <?php if (isset($_GET['success'])) { ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Signup Successful!',
                            showConfirmButton: true
                        }).then(function() {
                            window.location.href = 'login.php';
                        });
                    });
                    </script>
            <?php } ?> 

            <div class="content">
                <div class="logo-section">
                    <div class="logo">
                        <img class="logo-img" src="img/defaultImg/mainLogo2.png" alt="logo">
                    </div>
                </div>
                <div class="input-section">
                    <form method="post" action="" class="login" enctype="multipart/form-data" autocomplete="off">
                        <div class="input-group">
                            <div class="input-field">
                                <i class="fa-solid fa-user left"></i>
                                <input type="text" placeholder="Name *" name="name" id="name" required>
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-field">
                                <i class="fa-solid fa-envelope left"></i>
                                <input type="email" placeholder="Email *" name="email" id="email" required>
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-field">
                                <i class="fa-solid fa-key left"></i>
                                <input type="password" placeholder="Password *" name="password" id="password" required>
                                <i class="fa-regular fa-eye" id="togglePassword"></i>
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-field">
                                <i class="fa-solid fa-phone left"></i>
                                <input type="tel" placeholder="Phone *" name="phone" id="phone" required>
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-field">
                                <i class="fa-regular fa-address-card left"></i>
                                <input type="text" placeholder="Address *" name="address" id="address" required>
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-field">
                                <i class="fa-solid fa-image left"></i>
                                <input type="file"  name="profile_pic" id="profile_pic" accept="image/*">
                            </div>
                        </div>
                        <div class="user-selects">
                            <div class="input-field">
                                <input type="radio" id="carrier" name="userselects" value="carrier" checked>
                                <label for="carrier">Carrier</label>
                            </div>
                            <div class="input-field">
                                <input type="radio" id="consignor" name="userselects" value="consignor">
                                <label for="consignor">Consignor</label>
                            </div>
                        </div>
                        <div class="btn-field">
                            <button type="submit" name="signupBtn" value="signup">Sign Up</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="last">
                <small>Already have an account? <a href="login.php">Log in here!</a></small>
            </div>
        </div>
    </div>
    <script src="js/imgPreview.js"></script>
    <!-- <script src="js/formValidation.js"></script> -->
    <script>
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
                errors.push("Name must be only alphabetical and like Rojan Dumaru'");
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
            } else if (isNaN(phone)) {
                errors.push("Phone number must be numeric.");
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
        function validateEmail(email) {
                var re = /\S+@\S+\.\S+/;
                return re.test(email);
            }
    </script>
    <script src="js/showpwd.js"></script>
</body>
</html>
