<?php
// Displaying according to number of column except some column
function displayUser($result) {
    if ($result && mysqli_num_rows($result) > 0) {

        // Get the column names from the first row of the result
        $columns = array_keys(mysqli_fetch_assoc($result));
        mysqli_data_seek($result, 0); // Reset the result pointer to the beginning

        // Columns to exclude from the table
        $excludedColumns = ['password', 'img_srcs', 'id', 'reset_otp_hash', 'reset_otp_expires_at'];

        // Remove the excluded columns from the array of column names
        $columns = array_filter($columns, function($column) use ($excludedColumns) {
            return !in_array($column, $excludedColumns);
        });

        // Display column headers
        echo "<tr><th>SN</th>";
        foreach ($columns as $column) {
            echo "<th>" . strtoupper($column) . "</th>";
        }        
        echo "<th>ACTION</th>"; // Add a placeholder for the action column
        echo "</tr>";

        // Display table rows
        $i = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr><td>$i</td>";
            foreach ($row as $column => $value) {
                if (!in_array($column, $excludedColumns)) {
                    echo "<td>$value</td>";
                }
                if ($column === 'id') {
                    $id = $value; // Assign the id column value to the $id variable
                }
            }
            echo "<td class='td-center'>                        
                <form action='' method='post' class='deleteBtn'>
                    <input type='hidden' name='action' value='delete'>
                    <input type='hidden' name='id' value='" . $id . "'>
                    <button type='submit'>Delete</button>
                </form></td>";
            echo "</tr>";
            $i++;
        }

    } else {
        echo "<td>No data to display.</td>";
    }
}


?>
