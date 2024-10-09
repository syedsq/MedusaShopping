<?php
session_start();
include 'config.php';  // Include database connection

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo "Access denied. You must be an admin to access this page.";
    exit();
}

// Handle deletion request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $itemId = intval($_POST['id']);
    
    // Prepare the DELETE SQL statement for order_items first
    $deleteOrderItemsSQL = "DELETE FROM order_items WHERE product_id = ?";
    $stmtOrderItems = $conn->prepare($deleteOrderItemsSQL);
    $stmtOrderItems->bind_param("i", $itemId);
    $stmtOrderItems->execute();
    $stmtOrderItems->close();

    // Prepare the DELETE SQL statement for products
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $itemId);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Item deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting item: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit(); // Exit after processing the request
}

// Handle update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $itemId = intval($_POST['update_id']);
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $image_url = $_POST['image_url'];

    // Prepare the UPDATE SQL statement
    $sql = "UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("ssdsi", $name, $description, $price, $image_url, $itemId);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Item updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating item: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// Fetch all items from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Items</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .admin-container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        form {
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="number"], input[type="submit"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .edit, .delete, .save, .cancel {
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            margin-right: 10px;
            cursor: pointer;
        }

        .edit, .save {
            background-color: #28a745;
        }

        .delete {
            background-color: #dc3545;
        }

        .cancel {
            background-color: #ffc107;
        }

        .edit:hover, .save:hover {
            background-color: #218838;
        }

        .delete:hover {
            background-color: #c82333;
        }

        .cancel:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Manage Items</h1>

        <!-- Form to Add New Item -->
        <form action="add_item.php" method="POST">
            <label for="name">Item Name</label>
            <input type="text" name="name" required>

            <label for="description">Description</label>
            <input type="text" name="description" required>

            <label for="price">Price</label>
            <input type="number" name="price" step="0.01" required>

            <label for="image_url">Image URL</label>
            <input type="text" name="image_url" required>

            <input type="submit" value="Add Item">
        </form>

        <!-- Display List of Items -->
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price ($)</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?php echo $row['id']; ?>">
                            <td class="item-name"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="item-description"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td class="item-price"><?php echo number_format($row['price'], 2); ?></td>
                            <td><img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" width="50"></td>
                            <td>
                                <button class="edit" onclick="editItem(<?php echo $row['id']; ?>, this)">Edit</button>
                                <button class="delete" onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script>
        function editItem(itemId, button) {
            const row = button.closest('tr');
            const nameCell = row.querySelector('.item-name');
            const descriptionCell = row.querySelector('.item-description');
            const priceCell = row.querySelector('.item-price');

            // Check if we are in editing mode
            if (button.textContent === "Edit") {
                // Change the cell contents to input fields
                nameCell.innerHTML = `<input type="text" value="${nameCell.textContent}">`;
                descriptionCell.innerHTML = `<input type="text" value="${descriptionCell.textContent}">`;
                priceCell.innerHTML = `<input type="number" value="${parseFloat(priceCell.textContent).toFixed(2)}" step="0.01">`;

                // Change button text to "Save"
                button.textContent = "Save";
                button.classList.remove('edit');
                button.classList.add('save');
                button.onclick = function () {
                    saveItem(itemId, row);
                };

                // Change delete button to cancel
                const deleteButton = row.querySelector('.delete');
                deleteButton.textContent = "Cancel";
                deleteButton.classList.remove('delete');
                deleteButton.classList.add('cancel');
                deleteButton.onclick = function () {
                    cancelEdit(row);
                };
            }
        }

        function saveItem(itemId, row) {
    const nameCell = row.querySelector('.item-name input');
    const descriptionCell = row.querySelector('.item-description input');
    const priceCell = row.querySelector('.item-price input');

    // Get new values
    const name = nameCell.value;
    const description = descriptionCell.value;
    const price = parseFloat(priceCell.value);
    const imageCell = row.querySelector('.item-image img');
    const image_url = imageCell ? imageCell.src : ''; // Get the current image URL

    // Send AJAX request to save updated data
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "", true); // Send to the same file
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        const response = JSON.parse(xhr.responseText);
        alert(response.message);
        
        // If the save was successful, update the table cells
        if (response.status === 'success') {
            // Update table cells with new values
            row.querySelector('.item-name').textContent = name;
            row.querySelector('.item-description').textContent = description;
            row.querySelector('.item-price').textContent = price.toFixed(2);
            imageCell.src = image_url; // Update image source
            imageCell.alt = name; // Update alt text
        }
    }    
            // Revert buttons back to original state
            
            const editButton = row.querySelector('.save');
            editButton.textContent = "Edit";
            editButton.classList.remove('save');
            editButton.classList.add('edit');
            editButton.onclick = function () {
                editItem(itemId, editButton);
            };

            const cancelButton = row.querySelector('.cancel');
            cancelButton.textContent = "Delete";
            cancelButton.classList.remove('cancel');
            cancelButton.classList.add('delete');
            cancelButton.onclick = function () {
                confirmDelete(itemId);
                alert('An error occurred while saving the item.');
            };
    xhr.send(`update_id=${itemId}&name=${encodeURIComponent(name)}&description=${encodeURIComponent(description)}&price=${price}&image_url=${encodeURIComponent(image_url)}`);
}
        function cancelEdit(row) {
            const itemId = row.dataset.id;
            const nameCell = row.querySelector('.item-name');
            const descriptionCell = row.querySelector('.item-description');
            const priceCell = row.querySelector('.item-price');

            // Reset the cells to their original values
            nameCell.innerHTML = nameCell.textContent;
            descriptionCell.innerHTML = descriptionCell.textContent;
            priceCell.innerHTML = parseFloat(priceCell.textContent).toFixed(2);

            const editButton = row.querySelector('.save');
            editButton.textContent = "Edit";
            editButton.classList.remove('save');
            editButton.classList.add('edit');
            editButton.onclick = function () {
                editItem(itemId, editButton);
            };

            const cancelButton = row.querySelector('.cancel');
            cancelButton.textContent = "Delete";
            cancelButton.classList.remove('cancel');
            cancelButton.classList.add('delete');
            cancelButton.onclick = function () {
                confirmDelete(itemId);
            };
        }

        function confirmDelete(itemId) {
            if (confirm("Are you sure you want to delete this item?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "", true); // Send to the same file
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    const response = JSON.parse(xhr.responseText);
                    alert(response.message);
                    if (response.status === 'success') {
                        // Remove the row from the table
                        const row = document.querySelector(`tr[data-id='${itemId}']`);
                        if (row) {
                            row.remove();
                        }
                    }
                };
                xhr.send(`id=${itemId}`);
            }
        }
    </script>
</body>
</html>
