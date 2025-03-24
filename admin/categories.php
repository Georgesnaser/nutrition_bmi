<?php 
    include '../conx.php';
    include 'queries.php';
    include 'header.php';
    ob_start();
?>
    <div class="content">
        <h1>Manage Categories</h1>
        <button onclick="togglePopup('addPopup')">Add New Category</button>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($nbCategories > 0) {
                    // Fetch employee data
                    while ($row = $result3->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?=$row['cid']?></td>
                        <td><?=$row['cname']?></td>
                        <td><?=$row['description']?></td>
                        <td>
                            <button onclick="openUpdatePopup(<?=$row['cid']?>,'<?=$row['cname']?>','<?=$row['description']?>')">Update</button>
                            <button onclick="openDeletePopup(<?=$row['cid']?>)" class="reject-btn">Delete</button>
                        </td>
                    </tr>
                <?php
                    }
                }else {
                    echo "<tr><td colspan='3'>No categories found</td></tr>";
                }
                ?>
                <!-- Add more rows as needed -->
            </tbody>
        </table>
    </div>
</div>

<div class="popup-overlay" onclick="closePopup()"></div>

<!--  insert form -->
<div id="addPopup" class="popup">
    <div class="popup-header">Add New Category</div>
    <form method="POST" action="">
        <label for="newCategoryName">Category Name:</label>
        <input type="text" id="newCategoryName" name="newCategoryName">
        <label for="newCategoryDesc">Category Description:</label>
        <input type="text" id="newCategoryDesc" name="newCategoryDesc">
        <button type="submit">Add</button>
        <button type="button" onclick="closePopup()">Close</button>
    </form>
</div>

<!--  udpate form -->
<div id="updatePopup" class="popup">
    <div class="popup-header">Update Category</div>
    <form method="POST" action="">
        <input type="hidden" id="updateCategoryId" name="updateCategoryId">
        <label for="updateCategoryName">New Name:</label>
        <input type="text" id="updateCategoryName" name="updateCategoryName">
        <label for="updateCategoryDesc">New Description:</label>
        <input type="text" id="updateCategoryDesc" name="updateCategoryDesc">
        
        <button type="submit">Update</button>
        <button type="button" onclick="closePopup()">Close</button>
    </form>
</div>

<div id="deletePopup" class="popup">
    <div class="popup-header">Delete Category</div>
    <form method="POST" action="">
        <input type="hidden" id="deleteCategoryId" name="deleteCategoryId">
        <p>Are you sure you want to delete this category?</p>
        <button type="submit">Yes</button>
        <button type="button" onclick="closePopup()">No</button>
    </form>
</div>

<script>
function openUpdatePopup(id, name, description) {
    document.getElementById('updateCategoryId').value = id;
    document.getElementById('updateCategoryName').value = name;
    document.getElementById('updateCategoryDesc').value = description;
    togglePopup('updatePopup');
}

function openDeletePopup(id) {
    document.getElementById('deleteCategoryId').value = id;
    togglePopup('deletePopup');
}
</script>


<?php
// Handle new category insertion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newCategoryName'])) {
    $newCategoryName = trim($_POST['newCategoryName']);
    $newCategoryDesc = trim($_POST['newCategoryDesc']);

    if (!empty($newCategoryName) && !empty($newCategoryDesc)) {
        // Insert the new category into the database
        $sql = "INSERT INTO categories (cname, description) VALUES ('$newCategoryName', '$newCategoryDesc')";

        if ($conn->query($sql) === TRUE) {
            //echo "<script>alert('Category added successfully!');</script>";
            header("location: categories.php");
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Category name cannot be empty!');window.location.href='categories.php';</script>";
    }
}

// Handle category update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateCategoryId'], $_POST['updateCategoryName'])) {
    $updateCategoryId = intval($_POST['updateCategoryId']);
    $updateCategoryName = trim($_POST['updateCategoryName']);
    $updateCategoryDesc = trim($_POST['updateCategoryDesc']);

    if (!empty($updateCategoryName)) {
        $sql = "UPDATE categories SET cname = '$updateCategoryName', description='$updateCategoryDesc' WHERE cid = $updateCategoryId";

        if ($conn->query($sql) === TRUE) {
            //echo "<script>alert('Category updated successfully!');</script>";
            header("location: categories.php");
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Category name cannot be empty!');window.location.href='categories.php';</script>";
    }
}

// Handle category deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteCategoryId'])) {
    $deleteCategoryId = intval($_POST['deleteCategoryId']);

    $sql = "DELETE FROM categories WHERE cid = $deleteCategoryId";

    if ($conn->query($sql) === TRUE) {
        //echo "<script>alert('Category deleted successfully!');</script>";
        header("location: categories.php");
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>
</body>
</html>
