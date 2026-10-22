```php
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$errors = [];
$success = "";

$full_name = "";
$email = "";
$course = "";
$age = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $age = trim($_POST["age"] ?? "");

    if ($full_name === "") {
        $errors[] = "Full name is required.";
    } elseif (strlen($full_name) > 100) {
        $errors[] = "Full name must not exceed 100 characters.";
    }

    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address.";
    }

    if ($course === "") {
        $errors[] = "Course is required.";
    } elseif (strlen($course) > 50) {
        $errors[] = "Course must not exceed 50 characters.";
    }

    if (
        $age !== "" &&
        (!filter_var($age, FILTER_VALIDATE_INT) ||
         (int)$age < 1 || (int)$age > 120)
    ) {
        $errors[] = "Age must be a whole number between 1 and 120.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "INSERT INTO students (full_name, email, course, age)
             VALUES (?, ?, ?, NULLIF(?, ''))"
        );

        $stmt->bind_param("ssss", $full_name, $email, $course, $age);

        if ($stmt->execute()) {
            $success = "Student registered successfully!";
            $full_name = "";
            $email = "";
            $course = "";
            $age = "";
        } elseif ($stmt->errno === 1062) {
            $errors[] = "This email address is already registered.";
        } else {
            $errors[] = "Unable to register student. Please try again.";
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

    <title>Register Student | Student Management System</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            flex-shrink: 0;
            background: #172554;
            color: white;
            padding: 28px 18px;
        }

        .brand {
            font-size: 21px;
            font-weight: bold;
            line-height: 1.5;
            margin-bottom: 35px;
        }

        .brand span {
            color: #93c5fd;
        }

        .menu-title {
            color: #a5b4fc;
            font-size: 12px;
            text-transform: uppercase;
            margin: 25px 12px 12px;
        }

        .nav-link {
            display: block;
            padding: 13px 14px;
            color: #e2e8f0;
            text-decoration: none;
            border-radius: 7px;
            margin-bottom: 7px;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #1d4ed8;
            color: white;
        }

        .logout {
            margin-top: 35px;
            background: #991b1b;
        }

        .logout:hover {
            background: #b91c1c;
        }

        .main {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            background: white;
            padding: 22px 32px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .topbar h1 {
            margin: 0;
            font-size: 25px;
        }

        .welcome {
            color: #64748b;
            font-size: 14px;
        }

        .content {
            max-width: 1000px;
            margin: auto;
            padding: 32px;
        }

        .intro {
            margin-bottom: 25px;
        }

        .intro h2 {
            margin: 0 0 8px;
            font-size: 24px;
        }

        .intro p {
            color: #64748b;
            line-height: 1.6;
            margin: 0;
        }

        .form-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px #0f172a0a;
        }

        .form-card h3 {
            margin-top: 0;
            margin-bottom: 8px;
        }

        .form-description {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            font-size: 14px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 15px;
            background: white;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px #2563eb20;
        }

        .hint {
            font-size: 12px;
            color: #64748b;
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .button {
            display: inline-block;
            padding: 12px 18px;
            border: none;
            border-radius: 7px;
            background: #1d4ed8;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }

        .button:hover {
            background: #1e40af;
        }

        .button-secondary {
            background: #e2e8f0;
            color: #1e293b;
        }

        .button-secondary:hover {
            background: #cbd5e1;
        }

        .message {
            padding: 14px 16px;
            border-radius: 7px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .error ul {
            margin-bottom: 0;
        }

        footer {
            padding: 20px 32px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
        }

        @media (max-width: 650px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 18px;
            }

            .brand {
                margin-bottom: 15px;
            }

            .menu-title {
                margin-top: 15px;
            }

            .topbar {
                padding: 20px;
                align-items: flex-start;
                flex-direction: column;
            }

            .content {
                padding: 20px;
            }

            .form-card {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: auto;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <aside class="sidebar">

        <div class="brand">
            STUDENT<br>
            <span>MANAGEMENT SYSTEM</span>
        </div>

        <div class="menu-title">Main Menu</div>

        <a href="index.php" class="nav-link">Dashboard</a>

        <a href="add_student.php" class="nav-link active">
            Register Student
        </a>

        <a href="view_students.php" class="nav-link">
            View Students
        </a>

        <a href="logout.php" class="nav-link logout">Logout</a>

    </aside>

    <div class="main">

        <header class="topbar">
            <h1>Register Student</h1>

            <div class="welcome">
                Welcome,
                <strong>
                    <?= htmlspecialchars($_SESSION["username"]) ?>
                </strong>
            </div>
        </header>

        <main class="content">

            <section class="intro">
                <h2>Add a New Student</h2>
                <p>
                    Enter the student's details below to add them
                    to the student management database.
                </p>
            </section>

            <section class="form-card">

                <h3>Student Information</h3>

                <p class="form-description">
                    Fields marked with * are required.
                </p>

                <?php if ($success !== ""): ?>
                    <div class="message success">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="message error">
                        <strong>Please correct these errors:</strong>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="add_student.php">

                    <div class="form-grid">

                        <div class="form-group">
                            <label for="full_name">Full Name *</label>
                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                maxlength="100"
                                placeholder="Enter student's full name"
                                value="<?= htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                maxlength="100"
                                placeholder="student@example.com"
                                value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="course">Course *</label>
                            <input
                                type="text"
                                id="course"
                                name="course"
                                maxlength="50"
                                placeholder="e.g. Information Technology"
                                value="<?= htmlspecialchars($course, ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="age">Age</label>
                            <input
                                type="number"
                                id="age"
                                name="age"
                                min="1"
                                max="120"
                                placeholder="Enter age (optional)"
                                value="<?= htmlspecialchars($age, ENT_QUOTES, 'UTF-8') ?>"
                            >
                            <span class="hint">
                                Age is optional and must be between 1 and 120.
                            </span>
                        </div>

                    </div>

                    <div class="form-actions">
                        <button type="submit" class="button">
                            Register Student
                        </button>

                        <a href="view_students.php"
                           class="button button-secondary">
                            View Students
                        </a>

                        <a href="index.php"
                           class="button button-secondary">
                            Back to Dashboard
                        </a>
                    </div>

                </form>

            </section>

        </main>

        <footer>
            Student Management System &copy; <?= date("Y") ?>
        </footer>

    </div>

</div>

</body>
</html>
```