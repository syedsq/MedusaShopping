<?php
session_start();
include 'config.php';  // Include database connection

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo "Access denied. You must be an admin to access this page.";
    exit();
}

// Handle deletion request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $userId = intval($_POST['delete_id']);
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'User deleted successfully.']);
    exit();
}

// Handle update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $userId = intval($_POST['edit_id']);
    $username = $_POST['username'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $sql = "UPDATE users SET username = ?, email = ?, address = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $username, $email, $address, $userId);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'User updated successfully.']);
    exit();
}

// Handle adding new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_username'])) {
    $newUsername = $_POST['new_username'];
    $newEmail = $_POST['new_email'];
    $newAddress = $_POST['new_address'];
    $newPassword = password_hash("default_password", PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, address, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $newUsername, $newEmail, $newAddress, $newPassword);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'User added successfully.']);
    exit();
}

// Fetch all users
$sql = "SELECT id, username, email, address FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
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
            font-family: Arial, sans-serif;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #007bff;
            color: white;
            font-size: 16px;
        }

        table td {
            font-size: 14px;
        }

        .edit, .delete {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
        }

        .edit {
            background-color: #28a745;
        }

        .edit:hover {
            background-color: #218838;
        }

        .delete {
            background-color: #dc3545;
        }

        .delete:hover {
            background-color: #c82333;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Manage Users</h1>
        <form id="add-form">
            <label for="new_username">Username</label>
            <input type="text" name="new_username" required>
            <label for="new_email">Email</label>
            <input type="email" name="new_email" required>
            <label for="new_address">Address</label>
            <input type="text" name="new_address" required>
            <button type="submit">Add User</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr data-id="<?php echo $row['id']; ?>">
                    <td class="username"><?php echo htmlspecialchars($row['username']); ?></td>
                    <td class="email"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td class="address"><?php echo htmlspecialchars($row['address']); ?></td>
                    <td>
                        <button class="edit" onclick="editUser(<?php echo $row['id']; ?>, this)">Edit</button>
                        <button class="delete" onclick="deleteUser(<?php echo $row['id']; ?>)">Delete</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
        function editUser(userId, button) {
            const row = button.closest('tr');
            const usernameCell = row.querySelector('.username');
            const emailCell = row.querySelector('.email');
            const addressCell = row.querySelector('.address');

            if (button.textContent === "Edit") {
                usernameCell.innerHTML = `<input type="text" value="${usernameCell.textContent}">`;
                emailCell.innerHTML = `<input type="email" value="${emailCell.textContent}">`;
                addressCell.innerHTML = `<input type="text" value="${addressCell.textContent}">`;

                button.textContent = "Save";
                button.classList.add('save');
                button.onclick = () => saveUser(userId, row);
            }
        }

        function saveUser(userId, row) {
            const username = row.querySelector('.username input').value;
            const email = row.querySelector('.email input').value;
            const address = row.querySelector('.address input').value;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = () => {
                const response = JSON.parse(xhr.responseText);
                alert(response.message);
                if (response.status === 'success') {
                    row.querySelector('.username').textContent = username;
                    row.querySelector('.email').textContent = email;
                    row.querySelector('.address').textContent = address;
                    row.querySelector('.edit').textContent = "Edit";
                }
            };
            xhr.send(`edit_id=${userId}&username=${encodeURIComponent(username)}&email=${encodeURIComponent(email)}&address=${encodeURIComponent(address)}`);
        }

        function deleteUser(userId) {
            if (confirm("Are you sure you want to delete this user?")) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = () => {
                    const response = JSON.parse(xhr.responseText);
                    alert(response.message);
                    if (response.status === 'success') {
                        document.querySelector(`tr[data-id='${userId}']`).remove();
                    }
                };
                xhr.send(`delete_id=${userId}`);
            }
        }

        document.getElementById('add-form').addEventListener('submit', function (event) {
            event.preventDefault();
            const username = this.new_username.value;
            const email = this.new_email.value;
            const address = this.new_address.value;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = () => {
                const response = JSON.parse(xhr.responseText);
                alert(response.message);
                if (response.status === 'success') {
                    location.reload();
                }
            };
            xhr.send(`new_username=${encodeURIComponent(username)}&new_email=${encodeURIComponent(email)}&new_address=${encodeURIComponent(address)}`);
        });
    </script>
</body>
</html>
