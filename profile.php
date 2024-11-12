<?php
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
$table = ($userSelects == "carrier") ? "carrierdetails" : "consignordetails";

// Fetch user data from the database
$result = $conn->query("SELECT * FROM $table WHERE id = " . $_SESSION['id']);
$row = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = trim($_POST['name']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $newPassword = $_POST['password'];

    // Validation
    $reNameRegEx = '/^[A-Z][a-zA-Z]*(?: [A-Z][a-zA-Z]*)*$/';
    if (!preg_match($reNameRegEx, $name)) {
        $errors[] = "Name must be only alphabetical and like Rojan Dumaru.";
    }
    if (empty($name) || empty($email) || empty($contact) || empty($address)) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (!empty($newPassword) && (strlen($newPassword) < 8 || strlen($newPassword) > 24)) {
        $errors[] = "Password must be between 8 and 24 characters.";
    }
    if (!is_numeric($contact) || strlen($contact) !== 10) {
        $errors[] = "Contact must be a 10-digit numeric value.";
    }

    // If no errors, process the form
    if (empty($errors)) {
        $uploadedFilePath = "";
        
        if (!empty($_FILES['profile_pic']['name'])) {
            // Handling image upload
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $uploadDirectory = 'img/profileUploads/';
            $imgName = $_FILES['profile_pic']['name'];
            $imgExtension = pathinfo($imgName, PATHINFO_EXTENSION);
            
            if (!in_array($imgExtension, $allowedExtensions)) {
                $errors[] = "Invalid image format. Allowed formats: JPG, JPEG, PNG.";
            } else {
                $uploadedFilePath = $uploadDirectory . basename($imgName);
                if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadedFilePath)) {
                    $errors[] = "Failed to upload the new image.";
                }
            }
        }

        // Prepare the SQL update statement
        $updateSql = "UPDATE $table SET name = ?, contact = ?, email = ?, address = ?";
        $params = [$name, $contact, $email, $address];
        
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateSql .= ", password = ?";
            $params[] = $hashedPassword;
        }
        
        if (!empty($uploadedFilePath)) {
            $updateSql .= ", img_srcs = ?";
            $params[] = $uploadedFilePath;
        }
        
        $updateSql .= " WHERE id = ?";
        $params[] = $_SESSION['id'];

        // Execute the prepared statement
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param(str_repeat('s', count($params) - 1) . 'i', ...$params);

        if ($stmt->execute()) {
            // Update session variables
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['contact'] = $contact;
            $_SESSION['address'] = $address;
            if (!empty($uploadedFilePath)) {
                $_SESSION['profilePic'] = $uploadedFilePath;
            }
            header("Location: profile.php?success=1");
            exit;
        } else {
            $errors[] = "Database update failed.";
        }
        
        $stmt->close();
    }

    // Display errors on the same page
    if (!empty($errors)) {
        echo '<div class="error-message">' . implode("<br>", $errors) . '</div>';
    }   
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" type="text/css" href="css/profile.css">
    <link rel="stylesheet" href="css/sweetAlert.css">
</head>
<body>
    <a href="home.php" class="back-button">Back</a>
    <div class="container">
        <div class="profile-header">
            <div class="profile-picture">
                <img src="<?php echo htmlspecialchars($row['img_srcs']); ?>" alt="Profile Picture" id="PicPreview">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="file" name="profile_pic" id="pic" accept="image/*" style="display: none;" onchange="previewImage(event)">
                    <button type="button" class="edit-button" onclick="openFileInput()">Edit Picture</button>
            </div>
            <h1><?php echo htmlspecialchars($row['name']); ?></h1>
        </div>

        <div class="personal-details">
            <div class="editdetailbtn">
                <h2>Personal Details</h2>
                <button type="button" class="edit-button" id="editBtn" style="font-size: 22px;" onclick="enableEditAll()">Edit</button>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">Update successful!</div>
            <?php endif; ?>

                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact:</label>
                        <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($row['contact']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($row['address']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" placeholder="Enter new password" readonly>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="save-changes" value="Save Changes" style="background-color: #008369;">
                    </div>
                </form>
        </div>
    </div>

    <script src="js/sweetalert.js"></script>
    <script src="js/imageValidation.js"></script>
    <script src="js/imgPreview.js"></script>
    <script src="js/profile.js"></script>
</body>
</html>
