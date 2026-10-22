
<?php
session_start();
require_once __DIR__ . '/db.php';

// Redirect logged-in users to the dashboard.
if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$error = "";
$username = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Please enter your username and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT user_id, username, password
             FROM users
             WHERE username = ?
             LIMIT 1"
        );

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $user["username"];

            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Administrator Login | Student Management System</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
        }

        .brand {
            text-align: center;
            margin-bottom: 25px;
        }

        .brand-icon {
            width: 68px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            border-radius: 18px;
            background: #172554;
            color: white;
            font-size: 28px;
            font-weight: bold;
            box-shadow: 0 8px 20px rgba(23, 37, 84, 0.18);
        }

        .brand h1 {
            margin: 0 0 9px;
            color: #172554;
            font-size: 25px;
            line-height: 1.3;
        }

        .brand p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .login-card {
            background: white;
            padding: 32px;
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .login-card h2 {
            margin: 0 0 9px;
            color: #0f172a;
            font-size: 22px;
        }

        .card-description {
            margin: 0 0 25px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 13px 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 9px;
            color: #334155;
            font-size: 14px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
            color: #0f172a;
            background: white;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .login-button {
            width: 100%;
            border: none;
            border-radius: 8px;
            padding: 14px;
            margin-top: 5px;
            background: #1d4ed8;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }

        .login-button:hover {
            background: #1e40af;
        }

        .login-button:focus-visible {
            outline: 3px solid #93c5fd;
            outline-offset: 3px;
        }

        .security-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 22px;
            padding: 13px;
            background: #f8fafc;
            border-radius: 8px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
        }

        .security-icon {
            color: #1d4ed8;
            font-weight: bold;
        }

        .footer {
            margin-top: 24px;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            body {
                padding: 16px;
            }

            .login-card {
                padding: 24px 20px;
            }

            .brand h1 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    <main class="login-wrapper">

        <div class="brand">
            <div class="brand-icon" aria-hidden="true">SM</div>

            <h1>Student Management System</h1>

            <p>Manage student records with ease and efficiency.</p>
        </div>

        <section class="login-card">
            <h2>Administrator Login</h2>

            <p class="card-description">
                Sign in with your administrator account to continue.
            </p>

            <?php if ($error !== ""): ?>
                <div class="error-message" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="username">Username</label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        maxlength="100"
                        autocomplete="username"
                        placeholder="Enter your username"
                        value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <button type="submit" class="login-button">
                    Sign In
                </button>
            </form>

            <div class="security-note">
                <span class="security-icon" aria-hidden="true">●</span>

                <span>
                    Administrator access only. Please keep your login
                    credentials private.
                </span>
            </div>
        </section>

        <footer class="footer">
            Student Management System &copy; <?= date("Y") ?>
        </footer>

    </main>
</body>
</html>
