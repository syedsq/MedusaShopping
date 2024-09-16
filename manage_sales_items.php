<?php
session_start();
include 'config.php';  // Include database connection

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo "Access denied. You must be an admin to access this page.";
    exit();
}

// Fetch all items from the products table
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
            margin-bottom: 20px;
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

        input[type="text"], input[type="number"], input[type="submit"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .edit, .delete {
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            margin-right: 10px;
        }

        .edit {
            background-color: #28a745;
        }

        .delete {
            background-color: #dc3545;
        }

        .edit:hover {
            background-color: #218838;
        }

        .delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Manage Items</h1>

        <!-- Form to Add New Item -->
        <form action="add_item.php" method="POST" enctype="multipart/form-data">
            <label for="name">Item Name</label>
            <input type="text" name="name" required>

            <label for="description">Description</label>
            <input type="text" name="description" required>

            <label for="price">Price</label>
            <input type="number" name="price" step="0.01" required>

            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" required>

            <label for="image">Image URL</label>
            <input type="text" name="image" placeholder="URL or select file">

            <input type="submit" value="Add Item">
        </form>

        <!-- Display List of Items -->
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price ($)</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" width="100"></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo number_format($row['price'], 2); ?></td>
                            <td><?php echo (int)$row['quantity']; ?></td>
                            <td>
                                <a href="edit_item.php?id=<?php echo $row['id']; ?>" class="edit">Edit</a>
                                <a href="delete_item.php?id=<?php echo $row['id']; ?>" class="delete">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>

