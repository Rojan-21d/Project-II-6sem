<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/sweetalert.js"></script>
    <link rel="stylesheet" href="../css/addtable.css">
    <link rel="stylesheet" href="../css/sweetAlert.css">
    <title>Add Load</title>
</head>
<body>

<?php
// Check if the session has not started, then start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../backend/databaseconnection.php';

// Check if the user is not logged in
if (!isset($_SESSION['email'])) {
    // Redirect the user to the login page
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['signupBtn'])) {

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and validate form data
    $name = $_POST['name'];
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $distance = $_POST['distance'];
    $description = $_POST['description'];
    $weight = $_POST['weight'];

    // Validate form fields
    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($origin)) {
        $errors[] = "Origin is required.";
    }

    if (empty($destination)) {
        $errors[] = "Destination is required.";
    }

    if (empty($distance) || !is_numeric($distance)) {
        $errors[] = "Distance must be a numeric value.";
    }

    if (empty($weight) || !is_numeric($weight)) {
        $errors[] = "Weight must be a numeric value.";
    }
    
    // Correct the file input name from 'image' to 'load_pic'
    if (!empty($_FILES['load_pic']['name'])) {
        $allowed_formats = array('jpg', 'jpeg', 'png');
        $upload_directory = 'img/loadUploads/';
        $img_name = $_FILES['load_pic']['name'];
        $img_extension = pathinfo($img_name, PATHINFO_EXTENSION);

        // Validate the file extension
        if (!in_array(strtolower($img_extension), $allowed_formats)) {
            $errors[] = "Only JPG, JPEG, and PNG images are allowed.";
        } else {
            $uploaded_file_path = $upload_directory . $img_name;
            if (!move_uploaded_file($_FILES['load_pic']['tmp_name'], '../' . $uploaded_file_path)) {
                $errors[] = "Error uploading the image.";
            }
        }
    } else {
        $uploaded_file_path = 'img/defaultImg/loadimage.jpg';
    }


        
    // Display errors using SweetAlert
    if (!empty($errors)) {
        $errorMessages = join("<br>", $errors);
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Errors",
                html: `' . $errorMessages . '`,
            });
        </script>';
    } else {


        // Insert the data into the database
        $sql = "INSERT INTO loaddetails (name, origin, destination, distance, description, weight, status, consignor_id, img_srcs)
                        VALUES ('$name', '$origin', '$destination', '$distance', '$description', '$weight', 'notBooked', '{$_SESSION['id']}', '$uploaded_file_path')";
        $result = $conn->query($sql);

        if ($result) {
            // Redirect to the success page
            header("Location: addload.php?success=1");
            exit;
        } else {
            // Handle database insertion error
            echo '<script>
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Database Error: ' . $conn->error . '",
            });
            </script>';    
        }
    }
}
}
?>

    <div class="add-main">
        <h2>Add Load</h2>

        <?php if (isset($_GET['success'])) { ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Load Added Successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'addload.php';
                        }
                    });
                });
            </script>
        <?php } ?>

        <form action="" method="POST" enctype="multipart/form-data" class="addForm">
        <div class="data-input">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" >
        </div>
        <div class="data-input">
            <label for="origin">Origin:</label>
            <input type="text" id="origin" name="origin" >
        </div>
        <div class="data-input">
            <label for="destination">Destination:</label>
            <input type="text" id="destination" name="destination">
        </div>
        <div class="data-input">
            <label for="distance">Distance (KM):</label>
            <input type="number" id="distance" name="distance">
        </div>
        <div class="data-input">
            <label for="description">Description:</label>
            <input type="text" id="description" name="description">
        </div>
        <div class="data-input">
            <label for="weight">Weight (Tons):</label>
            <input type="number" id="weight" name="weight">
        </div>
        <div class="data-input center">
            <label for="load_pic">Image:</label>
            <input class="inpImg" type="file" id="load_pic" name="load_pic" accept="image/*" placeholder="Image">
        </div>
        <div class="button-input">
            <input type="hidden" name="id" value="">
        </div>
        <button type="submit" name="signupBtn">ADD LOAD</button><br>
        <a href="../"><button type="button">Home</button></a>
        </form>
    </div>
</body>
</html>

