
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid student ID.");
}

$stmt = $conn->prepare(
    "SELECT full_name, email, course, age FROM students WHERE std_id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    exit("Student not found.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $age = trim($_POST["age"] ?? "");

    if ($full_name === "" || $email === "" || $course === "") {
        $message = "Please fill in all required fields.";
    } elseif (strlen($full_name) > 100 || strlen($course) > 50) {
        $message = "The name or course is too long.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $message = "Please enter a valid email address.";
    } elseif ($age !== "" && filter_var($age, FILTER_VALIDATE_INT) === false) {
        $message = "Age must be a whole number.";
    } elseif ($age !== "" && ((int)$age < 1 || (int)$age > 120)) {
        $message = "Please enter a valid age.";
    } else {
        $studentAge = ($age === "") ? null : (int)$age;

        $stmt = $conn->prepare(
            "UPDATE students
             SET full_name = ?, email = ?, course = ?, age = ?
             WHERE std_id = ?"
        );
        $stmt->bind_param("sssii", $full_name, $email, $course, $studentAge, $id);

        try {
            $stmt->execute();
            $stmt->close();
            header("Location: view_students.php?updated=1");
            exit;
        } catch (mysqli_sql_exception $e) {
            $stmt->close();

            if ((int)$e->getCode() === 1062) {
                $message = "That email address is already registered to another student.";
            } else {
                $message = "Unable to update the student. Please try again.";
            }
        }
    }

    // Keep the submitted values in the form if validation fails.
    $student['full_name'] = $full_name;
    $student['email'] = $email;
    $student['course'] = $course;
    $student['age'] = $age;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student | Student Management System</title>

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
            margin: 0 0 8px;
        }

        .brand-subtitle {
            color: #cbd5e1;
            font-size: 13px;
            margin-bottom: 35px;
        }

        .nav-title {
            color: #94a3b8;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 25px 12px 12px;
        }

        .nav-link {
            display: block;
            color: #e2e8f0;
            text-decoration: none;
            padding: 13px 14px;
            margin-bottom: 7px;
            border-radius: 8px;
            font-size: 14px;
        }

        .nav-link:hover {
            background: #263968;
        }

        .nav-link.active {
            background: #1d4ed8;
            color: white;
        }

        .nav-link.logout {
            margin-top: 30px;
            color: #fecaca;
        }

        .main-content {
            flex: 1;
            min-width: 0;
            padding: 32px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .topbar h1 {
            margin: 0 0 7px;
            font-size: 28px;
            color: #0f172a;
        }

        .subtitle {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }

        .welcome {
            background: white;
            border: 1px solid #e2e8f0;
            padding: 11px 16px;
            border-radius: 10px;
            font-size: 14px;
        }

        .welcome strong {
            color: #1d4ed8;
        }

        .form-card {
            max-width: 850px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
        }

        .card-heading {
            margin: 0 0 8px;
            font-size: 21px;
            color: #0f172a;
        }

        .card-description {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin: 0 0 25px;
        }

        .message {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            padding: 13px 15px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 14px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .form-group {
            min-width: 0;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 9px;
            color: #334155;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            color: #0f172a;
            outline: none;
        }

        input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .hint {
            display: block;
            color: #64748b;
            font-size: 12px;
            margin-top: 7px;
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
            padding-top: 23px;
            border-top: 1px solid #e2e8f0;
        }

        .button {
            display: inline-block;
            border: none;
            border-radius: 8px;
            padding: 13px 20px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
        }

        .button-primary {
            background: #1d4ed8;
            color: white;
        }

        .button-primary:hover {
            background: #1e40af;
        }

        .button-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .button-secondary:hover {
            background: #cbd5e1;
        }

        .footer {
            margin-top: 30px;
            color: #94a3b8;
            font-size: 12px;
            text-align: center;
        }

        @media (max-width: 800px) {
            .layout {
                display: block;
            }

            .sidebar {
                width: 100%;
                padding: 20px;
            }

            .brand-subtitle {
                margin-bottom: 18px;
            }

            .nav-title {
                margin-top: 18px;
            }

            .nav-link.logout {
                margin-top: 10px;
            }

            .main-content {
                padding: 22px 16px;
            }

            .form-card {
                padding: 22px;
            }
        }

        @media (max-width: 520px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: auto;
            }

            .topbar h1 {
                font-size: 24px;
            }

            .form-actions {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
<div class="layout">

    <aside class="sidebar">
        <div class="brand">Student Manager</div>
        <div class="brand-subtitle">Student Management System</div>

        <div class="nav-title">Main Menu</div>

        <nav>
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="add_student.php" class="nav-link">Register Student</a>
            <a href="view_students.php" class="nav-link active">View Students</a>
            <a href="logout.php" class="nav-link logout">Logout</a>
        </nav>
    </aside>

    <main class="main-content">

        <div class="topbar">
            <div>
                <h1>Edit Student</h1>
                <p class="subtitle">Update the student's information below.</p>
            </div>

            <div class="welcome">
                Welcome, <strong><?= htmlspecialchars($_SESSION["username"] ?? "User", ENT_QUOTES, 'UTF-8') ?></strong>
            </div>
        </div>

        <section class="form-card">
            <h2 class="card-heading">Student Information</h2>
            <p class="card-description">
                Make the necessary changes and select Save Changes when you are finished.
                Fields marked with * are required.
            </p>

            <?php if ($message !== ""): ?>
                <div class="message" role="alert">
                    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-grid">

                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            maxlength="100"
                            autocomplete="name"
                            required
                            value="<?= htmlspecialchars($student['full_name'], ENT_QUOTES, 'UTF-8') ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            maxlength="100"
                            autocomplete="email"
                            required
                            value="<?= htmlspecialchars($student['email'], ENT_QUOTES, 'UTF-8') ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="course">Course *</label>
                        <input
                            type="text"
                            id="course"
                            name="course"
                            maxlength="50"
                            required
                            value="<?= htmlspecialchars($student['course'], ENT_QUOTES, 'UTF-8') ?>"
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
                            step="1"
                            value="<?= htmlspecialchars((string)($student['age'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        >
                        <span class="hint">Optional. Enter a whole number from 1 to 120.</span>
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="button button-primary">
                        Save Changes
                    </button>

                    <a href="view_students.php" class="button button-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </section>

        <div class="footer">
            Student Management System &copy; <?= date("Y") ?>
        </div>

    </main>
</div>
</body>
</html>
