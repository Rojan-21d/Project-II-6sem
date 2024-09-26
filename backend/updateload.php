<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <script src="../js/sweetalert.js"></script>
    <link rel="stylesheet" href="../css/sweetAlert.css">
    <link rel="stylesheet" href="../css/addtable.css">
    <script src="../js/imgPreview.js"></script>
    <title>Update Load</title>
</head>
<body>

<?php
// Check if the session has not started, then start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Check if the user is not logged in
if(!isset($_SESSION['email'])) {
    // Redirect the user to the login page or any other authentication page
    header("Location: login.php");
    exit;
}

// Include the file for database connection
require 'databaseconnection.php';

// Function to display alerts using SweetAlert
function showAlert($message, $type = 'error') {
    echo "<script>
        Swal.fire({
            icon: '$type',
            title: '$type',
            html: '$message',
        });
    </script>";
}

// Function to sanitize user input
function validateInput($data) {
    return htmlspecialchars(trim($data));
}

// Fetch the load details to display in form for editing
function fetchLoadDetails($conn, $loadId) {
    $sql = "SELECT * FROM loaddetails WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('i', $loadId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row;
    } else {
        showAlert("Load details fetching failed: " . $conn->error);
        return false;
    }
}

$row = fetchLoadDetails($conn, $_SESSION['load_id']);

// Handling form submission on POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    // Retrieving form data
    $name = $_POST["name"];
    $origin = $_POST["origin"];
    $destination = $_POST['destination'];
    $distance = $_POST['distance'];
    $description = $_POST['description'];
    $weight = $_POST['weight'];

    // Validating form fields
    $errors = [];
    if (empty($name) || empty($origin) || empty($destination) || empty($distance) || empty($weight)) {
        $errors[] = "All fields are required.";
    } 
    if (!is_numeric($distance)) {
        $errors[] = "Distance must be a numeric value.";
    }
    if (!is_numeric($weight)) {
        $errors[] = "Weight must be a numeric value.";
    }
    // Displaying errors using SweetAlert
    if (!empty($errors)) {
        $errorMessages = implode("<br>", $errors);
        showAlert($errorMessages);
    } else {
        $imageDestination = '';        
        // Process the uploaded image only if a new file is selected
        if (!empty($_FILES['image']['tmp_name'])) {
            // Check and delete the existing image if it's not a default image
            if (file_exists("../".$row['img_srcs']) && strpos($row['img_srcs'], 'defaultImg') == false){
                unlink("../".$row['img_srcs']);
            }
            $image = $_FILES['image'];
            $imageFileName = $image['name'];
            $imageTempName = $image['tmp_name'];
            $imageDestination = 'img/loadUploads/' . $imageFileName;                    
            // Move the uploaded image to a specific directory
            if (move_uploaded_file($imageTempName, "../". $imageDestination)) {
                // Image uploaded successfully
            } else {
                // Failed to upload image
                echo "<script>Swal.fire('Failed to upload image.');</script>";
            }
        }

        // Update load details in the database
        $sql = "UPDATE loaddetails SET
            name = ?,
            origin = ?,
            destination = ?,
            distance = ?,
            description = ?,
            weight = ?";

        $params = [$name, $origin, $destination, $distance, $description, $weight];
        // Update the image path only if a new image was uploaded
        if (!empty($imageDestination)) {
            $sql .= ", img_srcs = ?";
            $params[] = $imageDestination;
        }
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            if ($stmt->execute()) {
                showAlert('Updated Successfully', 'success');
            } else {
                showAlert('Update Failed: ' . 'error');                
            }
            $stmt->close();
        } else {
            showAlert('Update Query Preparation Failed: ' . 'error');            
        }
    }
}
$row = fetchLoadDetails($conn, $_SESSION['load_id']);
?>

<div class="add-main">
    <h2>Edit Load Details</h2>
    <form action="" method="POST" enctype="multipart/form-data" class="addForm">
        <div class="image-upload">
            <!-- <input class="inpImg" type="file" id="image" name="image" accept="image/*"> -->
            <img src="../<?php echo $row['img_srcs']; ?>" alt="Load Picture" id="PicPreview">
            <input type="file" name="image" id="pic" accept="image/*" style="display: none;" onchange="previewImage(event)">
            <button type="button" class="edit-button" onclick="openFileInput()">Edit</button>
        </div>
        <div class="data-input">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $row['name'] ?? ''; ?>">
        </div>
        <div class="data-input">
            <label for="origin">Origin:</label>
            <input type="text" id="origin" name="origin" value="<?php echo $row['origin'] ?? ''; ?>">
        </div>
        <div class="data-input">
            <label for="destination">Destination:</label>
            <input type="text" id="destination" name="destination" value="<?php echo $row['destination'] ?? ''; ?>">
        </div>
        <div class="data-input">
            <label for="distance">Distance (KM):</label>
            <input type="text" id="distance" name="distance" value="<?php echo $row['distance'] ?? ''; ?>">
        </div>
        <div class="data-input">
            <label for="description">Description:</label>
            <input type="text" id="description" name="description" value="<?php echo $row['description'] ?? ''; ?>">
        </div>
        <div class="data-input">
            <label for="weight">Weight (Tons):</label>
            <input type="number" id="weight" name="weight" value="<?php echo $row['weight'] ?? ''; ?>">
        </div>
        <div class="button-input">
            <input type="hidden" name="id" value="<?php echo $_SESSION['load_id']; ?>">
        </div>
        <button type="submit">Update</button><br>
        <a href="../"><button type="button">Home</button></a>
    </form>
</div>
<script>
        function openFileInput() {
        document.getElementById('pic').click();
    }
</script>
</body>
</html>
