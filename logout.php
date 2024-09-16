<?php
session_start();
session_destroy();  // Destroy all session data
header("Location: login.php");  // Redirect to login page
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
</head>
<body>
    <p>You have been logged out. <a href="login.php">Login again</a></p>
</body>
</html>
