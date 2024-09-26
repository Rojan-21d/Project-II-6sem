<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/sweetalert.js"></script>
    <link rel="stylesheet" href="../css/sweetAlert.css">
    <title>Add Load</title>
</head>
<body>

<?php

session_start();

// Check if the user is not logged in
if (!isset($_SESSION['email'])) {
    // Redirect the user to the login page or any other authentication page
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and validate form data
    $name = $_POST['name'];
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $distance = $_POST['distance'];
    $description = $_POST['description'];
    $weight = $_POST['weight'];

    // Validate form fields (you can add more specific validation rules)
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

    // Check if there are any validation errors
    // if (!empty($errors)) {
    //     // Handle validation errors (e.g., display error messages)
    //     foreach ($errors as $error) {
    //         echo "<p>Error: $error</p>";
    //     }
    // Display errors using SweetAlert

    if (!empty($errors)) {
        $errorMessages = join("<br>", $errors);
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Errors",
                html: `' . $errorMessages . '`,
            }).then(function() {
                window.location.href = "../layout/addload.php";
            });

        </script>';
    }else {
        // Process the uploaded image
        $image = $_FILES['image'];
        $imageFileName = $image['name'];
        $imageTempName = $image['tmp_name'];
        $imageDestination = 'img/loadUploads/' . $imageFileName;

        // Move the uploaded image to a specific directory
        move_uploaded_file($imageTempName, '../'.$imageDestination);

        // Insert the data into the database
        require 'databaseconnection.php';
        $sql = "INSERT INTO loaddetails (name, origin, destination, distance, description, weight, status, consignor_id, img_srcs)
                        VALUES ('$name', '$origin', '$destination', '$distance', '$description', '$weight', 'notBooked', '{$_SESSION['id']}', '$imageDestination')";
        $result = $conn->query($sql);

        if ($result) {
            // Redirect to the success page
            header("Location: ../layout/addload.php?success=1");
            exit;
        } else {
            // Handle database insertion error
            echo "<p>Error: " . $conn->error . "</p>";
        }
    }
}
?>
</body>
</html>