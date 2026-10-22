
<?php
session_start();

$_SESSION = [];
session_destroy();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logged Out</title>
</head>
<body>
    <h2>You have been logged out.</h2>
    <p><a href="login.php">Go to Login</a></p>
</body>
</html>
