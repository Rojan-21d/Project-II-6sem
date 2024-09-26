<?php
// Check if the session has not started, then start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$errors = [];

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require 'backend/databaseconnection.php';
include 'layout/header.php';
$userSelects = $_SESSION['usertype'];

if ($userSelects == "carrier") {
    $table = "carrierdetails";
} elseif ($userSelects == "consignor") {
    $table = "consignordetails";
}

// For displaying from DB
$result = $conn->query("SELECT * FROM $table WHERE id = " . $_SESSION['id']);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $newPassword = $_POST['password'];

    $reNameRegEx = '/^[A-Z][a-zA-Z]*(?: [A-Z][a-zA-Z]*)*$/';
    if (!preg_match($reNameRegEx, $name)) {
        $errors[] = "PHP Name must be only alphabetical and like Rojan Dumaru";
    }

    if (empty($name) || empty($email) || empty($contact) || empty($address)) {
        $errors[] = "PHP All fields are required";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "PHP Invalid email format";
    }

    if (!empty($newPassword) && (strlen($newPassword) < 8 || strlen($newPassword) > 24)) {
        $errors[] = "PHP Password must be between 8 and 24 characters";
    }
    if (!is_numeric($contact)){
        $errors[] = "Contact must be a numeric value.";
        if (strlen($contact) !== 10) {
            $errors[] = "PHP Contact Number Length must be 10";
        }
    }   

    if (empty($errors)) {
        if (!empty($_FILES['profile_pic']['name'])) {
            if (file_exists($row['img_srcs']) && strpos($row['img_srcs'], 'defaultImg') == false){
                unlink($row['img_srcs']);
            }
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $uploadDirectory = 'img/profileUploads/';
            
            $imgName = $_FILES['profile_pic']['name'];
            $imgExtension = pathinfo($imgName, PATHINFO_EXTENSION);
            
            if (!in_array($imgExtension, $allowedExtensions)) {
                $errors[] = "PHP Invalid image format. Allowed formats: JPG, JPEG, PNG.";
            } else {
                $uploadedFilePath = $uploadDirectory . $imgName;
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadedFilePath)) {
                    $updateSql = "UPDATE $table SET img_srcs = '$uploadedFilePath',";
                } else {
                    $errors[] = "PHP Failed to upload the new image";
                }
            }            
        } else {
        $updateSql = "UPDATE $table SET";
    }
    
    $updateSql .= " name = '$name', contact = '$contact', email = '$email', address = '$address'";
    
    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateSql .= ", password = '$hashedPassword'";
    }
    
    $updateSql = rtrim($updateSql, ',');
    $updateSql .= " WHERE id = " . $_SESSION['id'];
    
    if ($conn->query($updateSql) === TRUE) {
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['contact'] = $contact;
            $_SESSION['address'] = $address;
            
            if (!empty($uploadedFilePath)) {
                $_SESSION['profilePic'] = $uploadedFilePath;
            }
            // Success, redirect to profile with success parameter
            header("Location: profile.php?success=1");
            exit;
        } else {
            $errors[] = "PHP Database update failed";
        }
    }

    // Display errors on the same page
    if (!empty($errors)) {
        $errorMessages = implode("<br>", $errors);
        echo '<div class="error-message">' . $errorMessages . '</div>';
    }   
}
?>

<!DOCTYPE html>
<html>
    <head>
<title>User Profile</title>
    <link rel="stylesheet" type="text/css" href="css/profile.css">
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <link rel="stylesheet" href="css/sweetAlert.css">
    <script src="js/sweetalert.js"></script>
    <script src="js/imageValidation.js"></script>
    <script src="js/imgPreview.js"></script>
    <title>User Profile</title>
    <link rel="stylesheet" type="text/css" href="css/profile.css">
</head>
<body>
<div class="container">
    <h1>Your Profile</h1>
    <a href="home.php" class="back-button">Back</a>

    <?php if (isset($_GET['success'])) { ?>
        <div class="success-message">
            Update successful!
        </div>
    <?php } ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="profile-picture">
            <img src="<?php echo $row['img_srcs']; ?>" alt="Profile Picture" id="PicPreview">
            <input type="file" name="profile_pic" id="pic" accept="image/*" style="display: none;" onchange="previewImage(event)">
            <button type="button" class="edit-button" onclick="openFileInput()">Edit</button>
        </div>

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo isset($row['name']) ? $row['name'] : ''; ?>" readonly>
            <button type="button" class="edit-button" onclick="enableEdit('name')">Edit</button>    
        </div>
        <div class="form-group">
            <label for="contact">Contact:</label>
            <input type="text" id="contact" name="contact" value="<?php echo isset($row['contact']) ? $row['contact'] : ''; ?>" readonly>
            <button type="button" class="edit-button" onclick="enableEdit('contact')">Edit</button>
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo isset($row['address']) ? $row['address'] : ''; ?>" readonly>
            <button type="button" class="edit-button" onclick="enableEdit('address')">Edit</button>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo isset($row['email']) ? $row['email'] : ''; ?>" readonly>
            <button type="button" class="edit-button" onclick="enableEdit('email')">Edit</button>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter new password">
            <button type="button" class="edit-button" onclick="enableEdit('password')">Edit</button>
        </div>

        <div class="form-group">
            <input type="submit" value="Save Changes">
        </div>
    </form>
</div>
<script>
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
            // alert("Validation Errors:\n" + errorMessage);
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
    
    function openFileInput() {
        document.getElementById('pic').click();
    }
    
    function enableEdit(field) {
            document.getElementById(field).readOnly = false;
        }
        
    </script>
</body>
</html>