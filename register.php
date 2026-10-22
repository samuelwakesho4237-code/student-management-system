
<?php
require_once __DIR__ . '/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $message = "Please fill in all fields.";
    } elseif (strlen($username) > 50) {
        $message = "Username must not exceed 50 characters.";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (username, password) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $username, $hashedPassword);

        if ($stmt->execute()) {
            $message = "Account created successfully! You can now log in.";
        } elseif ($stmt->errno === 1062) {
            $message = "That username already exists.";
        } else {
            $message = "Unable to create account. Please try again.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Administrator</title>
</head>
<body>
    <h1>Create Administrator Account</h1>

    <?php if ($message !== ""): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <label>Username:</label><br>
        <input type="text" name="username" maxlength="50" required>
        <br><br>

        <label>Password (at least 8 characters):</label><br>
        <input type="password" name="password" minlength="8" required>
        <br><br>

        <button type="submit">Create Account</button>
    </form>

    <p><a href="login.php">Go to Login</a></p>
</body>
</html>
