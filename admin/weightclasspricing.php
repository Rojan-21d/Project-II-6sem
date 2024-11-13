<?php
require '../backend/databaseconnection.php';

// Fetch weight class pricing records
$query = "SELECT * FROM weight_class_pricing";
$result = $conn->query($query);

if ($result === false) {
    echo "Error: " . $conn->error;
    exit;
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];

    $deleteQuery = "DELETE FROM weight_class_pricing WHERE id = $deleteId";
    if ($conn->query($deleteQuery) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle form submission and update records
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && !isset($_POST['delete_id'])) {
    $id = $_POST['id'];
    $weight_class = $_POST['weight_class'];
    $min_weight = $_POST['min_weight'];
    $max_weight = $_POST['max_weight'];
    $base_price_min = $_POST['base_price_min'];
    $base_price_max = $_POST['base_price_max'];

    $updateQuery = "UPDATE weight_class_pricing SET
        weight_class = '$weight_class',
        min_weight = $min_weight,
        max_weight = $max_weight,
        base_price_min = $base_price_min,
        base_price_max = $base_price_max
        WHERE id = $id";

    if ($conn->query($updateQuery) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weight Class Pricing</title>
    <link rel="stylesheet" href="../css/weightclasspricing.css">
</head>
<body>

<div class="header">
    <h1>Weight Class Pricing</h1>
    <a href="adminpanel.php" class="home-button">Home</a>
</div>

<table>
    <thead>
        <tr>
            <th>Weight Class (Tons)</th>
            <th>Min Weight</th>
            <th>Max Weight</th>
            <th>Base Price Min</th>
            <th>Base Price Max</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <!-- Update form for each row -->
            <form method="POST" action="weightclasspricing.php">
                <td>
                    <input type="text" name="weight_class" value="<?php echo $row['weight_class']; ?>" required>
                </td>
                <td>
                    <input type="number" name="min_weight" value="<?php echo $row['min_weight']; ?>" required>
                </td>
                <td>
                    <input type="number" name="max_weight" value="<?php echo $row['max_weight']; ?>" required>
                </td>
                <td>
                    <input type="number" name="base_price_min" value="<?php echo $row['base_price_min']; ?>" required>
                </td>
                <td>
                    <input type="number" name="base_price_max" value="<?php echo $row['base_price_max']; ?>" required>
                </td>
                <td>
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit">Update</button>

                    <!-- Delete form for each row (inside the same table cell) -->
                    <form method="POST" action="weightclasspricing.php" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this weight class pricing?');" style="background-color: red; color: white;">Delete</button>
                    </form>
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
