<?php 
include '../conx.php';
include 'queries.php';
include 'header.php';
ob_start();
?>
<div class="content">
    <h1>Import CSV Data</h1>
    <!-- Form to upload CSV -->
    <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    
                    <div class="card-body">
                        <!-- Form to upload CSV -->
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="csvFile" class="form-label">Choose CSV file:</label>
                                <input type="file" name="csvFile" id="csvFile" accept=".csv" class="form-control" required>
                            </div>
                            <button type="submit" name="submit" class="btn btn-success w-100">Import CSV</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

</div>
</body>
</html>

<?php

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Check if a file was uploaded
    if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
        // File path
        $file = $_FILES['csvFile']['tmp_name'];


        // Open the CSV file for reading
        if (($handle = fopen($file, 'r')) !== FALSE) {
            // Skip the first row (headers) if necessary
            fgetcsv($handle);

            // Loop through each row of the CSV
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                // Extract the values from the CSV row
                $iname = $data[0];
                $calories = $data[1];
                $categoryID = $data[2];

                // Insert data into the items table
                $sql = "INSERT INTO items (iname, calories, categoryID) VALUES ('$iname', '$calories', '$categoryID')";

                // Execute the query
                if (!$conn->query($sql)) {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }

            // Close the file
            fclose($handle);

            echo "Data imported successfully!";
        } else {
            echo "Error opening the file.";
        }

        // Close the database connection
        $conn->close();
    } else {
        echo "No file uploaded or an error occurred.";
    }
}
?>
