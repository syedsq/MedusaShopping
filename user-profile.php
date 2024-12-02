<?php
session_start();
include 'config.php';  // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view your profile.";
    exit();
}

$user_id = $_SESSION['user_id'];  // Logged-in user's ID
$message = "";  // Feedback message

// Initialize order details
$first_name = $last_name = $address = $city = $state = $zip = "";

// Fetch order details via a join with the `orders` table
$sql = "
    SELECT od.first_name, od.last_name, od.address, od.city, od.state, od.zip
    FROM order_details od
    JOIN orders o ON o.id = od.order_id
    WHERE o.user_id = ?
";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL error during prepare: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $address, $city, $state, $zip);
$stmt->fetch();
$stmt->close();

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];

    // Update the `order_details` table with new details
    $update_sql = "
        UPDATE order_details od
        JOIN orders o ON o.id = od.order_id
        SET od.first_name = ?, od.last_name = ?, od.address = ?, od.city = ?, od.state = ?, od.zip = ?
        WHERE o.user_id = ?
    ";
    $update_stmt = $conn->prepare($update_sql);

    if (!$update_stmt) {
        die("SQL error during prepare: " . $conn->error);
    }

    $update_stmt->bind_param("ssssssi", $first_name, $last_name, $address, $city, $state, $zip, $user_id);

    if ($update_stmt->execute()) {
        $message = "Your details has been updated successfully!";
    } else {
        $message = "Error updating user details: " . $update_stmt->error;
    }
    $update_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        <?php include 'CSS/profile.css'; ?>
        <?php include 'CSS/styles.css'; ?>
        body {
            background-repeat:no-repeat;
            background-size: cover;
            background-attachment: fixed;
            background-image: url('background/user-background2.jpg');
        }
        .success-message {
            color: green;
            margin: 10px 0;
            font-size: 14px;
            background-color: #e6ffe7;
            padding: 10px;
            border: 1px solid green;
            border-radius: 5px;  
        }
        .error-message a{
            color: rgb(124, 9, 9);
            text-decoration: none;
            font-weight: 800;
        }
        
    </style>
</head>
<body>
    <nav class="navbar">
        <ul>
            <li class="logo">
                <a class="main_page" href="index.php">
                    <img class="image" src="icon-image/logo.png" alt="Logo">Medusa Gym</a>
            </li>
            <li class="toggle-button">
                <a href="#">
                    <img class="image" src="icon-image/toggle-icon.png" alt="toggle" style="vertical-align: middle">
                </a>
            </li>
            <div class="nav-items">
                <li><a class="NavButton" href="product.php">Browse</a></li>
                <li><span class="login_welcome">Welcome, <?php echo htmlspecialchars($first_name); ?>!</span></li>
                <li><a class="NavLogout" href="logout.php">Logout</a></li>
            </div>
        </ul>
    </nav>

    <div class="Profile">
        <div class="title">
            <h4>User Profile</h4>
        </div>
        <form method="POST" action="">
            <div class="name">
                <div class="firstname">
                    <label class="labels">First Name</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                </div>
                <div class="lastname">
                    <label class="labels">Last Name</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                </div>
            </div>
            <div class="address">
                <label class="labels">Address</label>
                <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>" required>
            </div>
            <div class="city-state-zip">
                <div class="city">
                    <label class="labels">City</label>
                    <input type="text" name="city" value="<?php echo htmlspecialchars($city); ?>" required>
                </div>
                <div class="state">
                    <label class="labels">State</label>
                    <input type="text" name="state" value="<?php echo htmlspecialchars($state); ?>" required>
                </div>
                <div class="zip">
                    <label class="labels">Zip Code</label>
                    <input type="text" name="zip" value="<?php echo htmlspecialchars($zip); ?>" required>
                </div>
            </div>
            <div class="update-profile-button">
                <button class="submit" type="submit">Save Changes</button>
            </div>
        </form>
        <?php if ($message == "Your details has been updated successfully!"): ?>
            <p class=success-message><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
