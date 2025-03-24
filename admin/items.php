<?php 
    include '../conx.php';
    include 'queries.php';
    include 'header.php';
    ob_start();
?>

    <div class="content">
        <h1>Manage Items</h1>
        <br></br>
        <input type="text" id="searchInput" class="form-control form-control-lg mb-3" placeholder="Search for ID, Name, Calories, Category..." style="width: 100%; max-width:500px; height:45px;">
        <br></br>
        <button onclick="togglePopup('addPopup')">Add New Item</button>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Calories</th>
                    <th>Category Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="itemsTable">
            <?php
                if ($nbItems > 0) {
                    // Fetch item data
                    while ($row = $result2->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?=$row['itemID']?></td>
                        <td><?=$row['iname']?></td>
                        <td><?=$row['calories']?></td>
                        <td><?=$row['cname']?></td>
                        <td>
                            <button onclick="openUpdatePopup(<?=$row['itemID']?>,'<?=$row['iname']?>','<?=$row['calories']?>','<?=$row['categoryID']?>')">Update</button>
                            <button onclick="openDeletePopup(<?=$row['itemID']?>)" class="reject-btn">Delete</button>
                        </td>
                    </tr>
                <?php
                    }
                }else {
                    echo "<tr><td colspan='3'>No categories found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>


<div class="popup-overlay" onclick="closePopup()"></div>

<!--  insert form -->
<div id="addPopup" class="popup">
    <div class="popup-header">Add New Item</div>
    <form method="POST" action="">
        <label for="newItemName">Item Name:</label>
        <input type="text" id="newItemName" name="newItemName">
        <label for="newItemCalories">Item Calories:</label>
        <input type="number" id="newItemCalories" name="newItemCalories">
        <label for="newItemCategory">Item Category:</label>
        <select id="newItemCategory" name="newItemCategory" required>
            <?php
                while ($row = $result3->fetch_assoc()) {
                    echo "<option value='" . $row['cid'] . "'>" . $row['cname'] . "</option>";
                }

            ?>
        </select>
        <div>
        <button type="submit" name="submit">Add</button>
        <button type="button" onclick="closePopup()">Close</button>
        </div>
    </form>
</div>

<!--  udpate form -->
<div id="updatePopup" class="popup">
    <div class="popup-header">Update Item</div>
    <form method="POST" action="">
        <input type="hidden" id="updateItemId" name="updateItemId">
        <label for="updateItemName">New Name:</label>
        <input type="text" id="updateItemName" name="updateItemName">
        <label for="updateItemCalories">Item Calories:</label>
        <input type="number" id="updateItemCalories" name="updateItemCalories">
        <label for="updateItemCategory">Item Category:</label>
        <select id="updateItemCategory" name="updateItemCategory" >
            <?php
            $sql = "SELECT cid, cname FROM categories";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                $selected = ($row['cid'] == $updateItemCategory) ? "selected" : "";
                echo "<option value='" . $row['cid'] . "' $selected>" . $row['cname'] . "</option>";
            }
            ?>
        </select>
        <div>
        <button type="submit" name="submit">Update</button>
        <button type="button" onclick="closePopup()">Close</button>
        </div>
    </form>
</div>


<div id="deletePopup" class="popup">
    <div class="popup-header">Delete Item</div>
    <form method="POST" action="">
        <input type="hidden" id="deleteItemId" name="deleteItemId">
        <p>Are you sure you want to delete this Item?</p>
        <button type="submit">Yes</button>
        <button type="button" onclick="closePopup()">No</button>
    </form>
</div>

<?php
// Handle new item insertion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newItemName'])) {
    $newItemName = trim($_POST['newItemName']);
    $newItemCalories = intval($_POST['newItemCalories']);
    $newItemCategory = intval($_POST['newItemCategory']);

    if (!empty($newItemName)&& !empty($newItemCalories)&& !empty($newItemCategory)) {
        // Insert the new item into the database
        $sql = "INSERT INTO items (iname, calories ,categoryID ) VALUES ('$newItemName', '$newItemCalories' , $newItemCategory)";

        if ($conn->query($sql) === TRUE) {
            //echo "<script>alert('item added successfully!');</script>";
            header("location: items.php");
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('item name cannot be empty!'); window.location.href='items.php';</script>";
    }
}

// Handle item update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateItemId'])) {
    $updateItemId = intval($_POST['updateItemId']);
    $updateItemName = trim($_POST['updateItemName']);
    $updateItemCategory = intval($_POST['updateItemCategory']);
    $updateItemCalories = intval($_POST['updateItemCalories']);

    if (!empty($updateItemName) && $updateItemCalories > 0 && $updateItemCategory > 0) {
        //$sql = "UPDATE items SET iname = '$updateItemName', calories = $updateItemCalories , categoryID='$updateItemCategory' WHERE itemID = $updateItemId";
        $sql = "UPDATE items SET iname = '$updateItemName', calories = $updateItemCalories , categoryID = $updateItemCategory WHERE itemID = $updateItemId";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('item updated successfully!'); window.location.href='items.php';</script>";
            //header("location: items.php");
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('item name cannot be empty!'); window.location.href='items.php';</script>";
    }
}

// Handle item deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteItemId'])) {
    $deleteItemId = intval($_POST['deleteItemId']);

    $sql = "DELETE FROM items WHERE itemID = $deleteItemId";

    if ($conn->query($sql) === TRUE) {
        //echo "<script>alert('Category deleted successfully!');</script>";
        header("location: items.php");
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>
<script>
    document.getElementById('searchInput').addEventListener('input', function(){
        const filter = this.value.toLowerCase();
            const rows =document.querySelectorAll('#itemsTable > tr');

            rows.forEach(row=>{
            const cells = row.querySelectorAll('td');
            const rowText =Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ');

            if(rowText.includes(filter)){
                row.style.display = '';
            }else{
                row.style.display = 'none';
            }
        });
    });
function openUpdatePopup(id, name, calories, category) {
    document.getElementById('updateItemId').value = id;
    document.getElementById('updateItemName').value = name;
    document.getElementById('updateItemCalories').value = calories;
    document.getElementById('updateItemCategory').value = category;
    togglePopup('updatePopup');
}

function openDeletePopup(id) {
    document.getElementById('deleteItemId').value = id;
    togglePopup('deletePopup');
}
</script>
</body>
</html>
