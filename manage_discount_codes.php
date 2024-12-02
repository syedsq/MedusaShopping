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
    $codeId = intval($_POST['delete_id']);
    $sql = "DELETE FROM discount_codes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codeId);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'Discount code deleted successfully.']);
    exit();
}

// Handle update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $codeId = intval($_POST['edit_id']);
    $code = $_POST['code'];
    $discount_percentage = floatval($_POST['discount_percentage']);
    $sql = "UPDATE discount_codes SET code = ?, discount_percentage = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdi", $code, $discount_percentage, $codeId);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'Discount code updated successfully.']);
    exit();
}

// Handle adding new discount code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_code'])) {
    $newCode = $_POST['new_code'];
    $newDiscountPercentage = floatval($_POST['new_discount_percentage']);
    $sql = "INSERT INTO discount_codes (code, discount_percentage) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sd", $newCode, $newDiscountPercentage);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'Discount code added successfully.']);
    exit();
}

// Fetch all discount codes
$sql = "SELECT id, code, discount_percentage FROM discount_codes";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Discount Codes</title>
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

        input[type="text"], input[type="number"], button {
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
        <h1>Manage Discount Codes</h1>
        <form id="add-form">
            <label for="new_code">Discount Code</label>
            <input type="text" name="new_code" required>
            <label for="new_discount_percentage">Discount Percentage</label>
            <input type="number" name="new_discount_percentage" step="0.01" required>
            <button type="submit">Add Discount Code</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Discount Percentage</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr data-id="<?php echo $row['id']; ?>">
                    <td class="code"><?php echo htmlspecialchars($row['code']); ?></td>
                    <td class="discount_percentage"><?php echo htmlspecialchars($row['discount_percentage']); ?></td>
                    <td>
                        <button class="edit" onclick="editDiscount(<?php echo $row['id']; ?>, this)">Edit</button>
                        <button class="delete" onclick="deleteDiscount(<?php echo $row['id']; ?>)">Delete</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
        function editDiscount(discountId, button) {
            const row = button.closest('tr');
            const codeCell = row.querySelector('.code');
            const discountCell = row.querySelector('.discount_percentage');

            // Switch to edit mode
            if (button.textContent === "Edit") {
                codeCell.innerHTML = `<input type="text" value="${codeCell.textContent}">`;
                discountCell.innerHTML = `<input type="number" value="${discountCell.textContent}" step="0.01">`;

                button.textContent = "Save";
                button.classList.add('save');
                button.onclick = () => saveDiscount(discountId, row);
            }
        }

        function saveDiscount(discountId, row) {
            const code = row.querySelector('.code input').value;
            const discountPercentage = parseFloat(row.querySelector('.discount_percentage input').value);

            // Send AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = () => {
                const response = JSON.parse(xhr.responseText);
                alert(response.message);
                if (response.status === 'success') {
                    row.querySelector('.code').textContent = code;
                    row.querySelector('.discount_percentage').textContent = discountPercentage.toFixed(2);
                    row.querySelector('.edit').textContent = "Edit";
                }
            };
            xhr.send(`edit_id=${discountId}&code=${encodeURIComponent(code)}&discount_percentage=${discountPercentage}`);
        }

        function deleteDiscount(discountId) {
            if (confirm("Are you sure you want to delete this discount code?")) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = () => {
                    const response = JSON.parse(xhr.responseText);
                    alert(response.message);
                    if (response.status === 'success') {
                        document.querySelector(`tr[data-id='${discountId}']`).remove();
                    }
                };
                xhr.send(`delete_id=${discountId}`);
            }
        }

        document.getElementById('add-form').addEventListener('submit', function (event) {
            event.preventDefault();
            const code = this.new_code.value;
            const discountPercentage = parseFloat(this.new_discount_percentage.value);

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
            xhr.send(`new_code=${encodeURIComponent(code)}&new_discount_percentage=${discountPercentage}`);
        });
    </script>
</body>
</html>
