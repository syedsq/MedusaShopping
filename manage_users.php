<?php
session_start();
include 'config.php';  // Include database connection

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo "Access denied. You must be an admin to access this page.";
    exit();
}

// Handle deletion request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $itemId = intval($_POST['id']);

    // Delete the product from the database
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $itemId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting product: ' . $stmt->error]);
    }

    $stmt->close();
    exit();
}

// Handle update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $itemId = intval($_POST['id']);
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $image_filename = $_POST['image'];

    // Update the product in the database
    $sql = "UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdiss", $name, $description, $price, $quantity, $image_filename, $itemId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Product updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating product: ' . $stmt->error]);
    }

    $stmt->close();
    exit();
}

// Handle add item request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $image_filename = $_POST['image'];

    // Add new product to the database
    $sql = "INSERT INTO products (name, description, price, quantity, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdiss", $name, $description, $price, $quantity, $image_filename);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Product added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error adding product: ' . $stmt->error]);
    }

    $stmt->close();
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
    <title>Manage Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
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

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        form input {
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            flex: 1;
        }

        form button {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        .actions button {
            padding: 5px 10px;
            margin: 2px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .actions .edit {
            background-color: #ffc107;
            color: #fff;
        }

        .actions .delete {
            background-color: #dc3545;
            color: #fff;
        }

        .actions .save {
            background-color: #28a745;
            color: #fff;
        }

        .actions .cancel {
            background-color: #6c757d;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Manage Products</h1>

        <!-- Add Item Form -->
        <form id="add-form">
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="description" placeholder="Description" required>
            <input type="number" name="price" placeholder="Price" step="0.01" required>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <input type="text" name="image" placeholder="Image Filename" required>
            <button type="submit">Add Product</button>
        </form>

        <!-- Product List -->
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?php echo $row['id']; ?>">
                            <td class="name"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="description"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td class="price"><?php echo number_format($row['price'], 2); ?></td>
                            <td class="quantity"><?php echo $row['quantity']; ?></td>
                            <td class="image"><?php echo htmlspecialchars($row['image']); ?></td>
                            <td class="actions">
                                <button class="edit">Edit</button>
                                <button class="delete">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No products available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.getElementById('add-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add');

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    location.reload();
                }
            });
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('edit')) {
                handleEdit(e.target.closest('tr'));
            } else if (e.target.classList.contains('delete')) {
                handleDelete(e.target.closest('tr'));
            }
        });

        function handleEdit(row) {
            const id = row.dataset.id;
            const name = row.querySelector('.name').textContent;
            const description = row.querySelector('.description').textContent;
            const price = parseFloat(row.querySelector('.price').textContent);
            const quantity = parseInt(row.querySelector('.quantity').textContent, 10);
            const image = row.querySelector('.image').textContent;

            row.innerHTML = `
                <td><input type="text" class="edit-name" value="${name}"></td>
                <td><input type="text" class="edit-description" value="${description}"></td>
                <td><input type="number" class="edit-price" value="${price}" step="0.01"></td>
                <td><input type="number" class="edit-quantity" value="${quantity}"></td>
                <td><input type="text" class="edit-image" value="${image}"></td>
                <td>
                    <button class="save">Save</button>
                    <button class="cancel">Cancel</button>
                </td>
            `;

            row.querySelector('.save').addEventListener('click', function () {
                const updatedName = row.querySelector('.edit-name').value;
                const updatedDescription = row.querySelector('.edit-description').value;
                const updatedPrice = parseFloat(row.querySelector('.edit-price').value);
                const updatedQuantity = parseInt(row.querySelector('.edit-quantity').value, 10);
                const updatedImage = row.querySelector('.edit-image').value;

                fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'edit',
                        id,
                        name: updatedName,
                        description: updatedDescription,
                        price: updatedPrice,
                        quantity: updatedQuantity,
                        image: updatedImage
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            });

            row.querySelector('.cancel').addEventListener('click', function () {
                location.reload(); // Cancel and reload to reset the table
            });
        }

        function handleDelete(row) {
            const id = row.dataset.id;
            if (confirm('Are you sure you want to delete this product?')) {
                fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ action: 'delete', id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        row.remove();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }
    </script>
</body>
</html>
