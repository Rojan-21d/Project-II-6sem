<?php
require '../backend/databaseconnection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the updated values from the form
    $configUpdates = $_POST['config'];

    foreach ($configUpdates as $id => $value) {
        // Update the configuration values in the database
        $stmt = $conn->prepare("UPDATE pricing_config SET config_value = ? WHERE id = ?");
        $stmt->bind_param("di", $value, $id); // Bind the parameters (value, id)
        $stmt->execute();
    }

    // Redirect after updating
    header("Location: viewpricingconfig.php"); // Refresh the page to show updated values
    exit;
}

// Fetch the existing configuration data
$result = $conn->query("SELECT * FROM pricing_config");
if ($result === false) {
    die("Error fetching configuration data: " . $conn->error);
}

$configs = [];
while ($row = $result->fetch_assoc()) {
    $configs[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price Configuration</title>
    <link rel="stylesheet" href="../css/priceconfig.css"> <!-- Optional: Add CSS for styling -->
</head>
<body>

<div class="header">
    <h1>Price Configuration</h1>
    <a href="adminpanel.php" class="home-button">Home</a>
</div>

<form method="POST" action="">
    <table>
        <thead>
            <tr>
                <th>Config Name</th>
                <th>Config Value</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($configs as $config): ?>
                <tr>
                    <td><?php echo htmlspecialchars($config['config_name']); ?></td>
                    <td>
                        <input type="number" step="0.01" name="config[<?php echo $config['id']; ?>]" value="<?php echo htmlspecialchars($config['config_value']); ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button type="submit">Save Changes</button>
</form>

</body>
</html>
