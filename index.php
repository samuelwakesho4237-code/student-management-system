```php
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$result = $conn->query("SELECT COUNT(*) AS total FROM students");

if (!$result) {
    die("Error retrieving student count: " . $conn->error);
}

$totalStudents = $result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Student Management System</title>

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
            background: #172554;
            color: white;
            padding: 28px 18px;
            flex-shrink: 0;
        }

        .brand {
            font-size: 21px;
            font-weight: bold;
            margin-bottom: 35px;
            line-height: 1.5;
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
            transition: 0.2s;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #e2e8f0;
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
            padding: 32px;
            max-width: 1200px;
            margin: auto;
        }

        .intro {
            margin-bottom: 28px;
        }

        .intro h2 {
            margin: 0 0 8px;
            font-size: 24px;
        }

        .intro p {
            color: #64748b;
            margin: 0;
            line-height: 1.6;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(
                auto-fit,
                minmax(220px, 1fr)
            );
            gap: 22px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px #0f172a0a;
            border: 1px solid #e2e8f0;
        }

        .stat-label {
            color: #64748b;
            font-size: 15px;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 42px;
            font-weight: bold;
            color: #1d4ed8;
        }

        .stat-note {
            color: #64748b;
            font-size: 13px;
            margin-top: 8px;
        }

        .section-title {
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 20px;
        }

        .actions {
            display: grid;
            grid-template-columns: repeat(
                auto-fit,
                minmax(220px, 1fr)
            );
            gap: 18px;
            margin-top: 20px;
        }

        .action-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 22px;
        }

        .action-card h3 {
            margin-top: 0;
        }

        .action-card p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            min-height: 44px;
        }

        .button {
            display: inline-block;
            padding: 11px 16px;
            border-radius: 7px;
            text-decoration: none;
            background: #1d4ed8;
            color: white;
            font-size: 14px;
            font-weight: bold;
            transition: 0.2s;
        }

        .button:hover {
            background: #1e40af;
        }

        footer {
            padding: 20px 32px;
            color: #64748b;
            font-size: 13px;
            text-align: center;
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

        <a href="index.php" class="nav-link active">
            Dashboard
        </a>

        <a href="add_student.php" class="nav-link">
            Register Student
        </a>

        <a href="view_students.php" class="nav-link">
            View Students
        </a>

        <a href="logout.php" class="nav-link logout">
            Logout
        </a>

    </aside>

    <div class="main">

        <header class="topbar">
            <h1>Dashboard</h1>

            <div class="welcome">
                Welcome,
                <strong>
                    <?= htmlspecialchars($_SESSION["username"]) ?>
                </strong>
            </div>
        </header>

        <main class="content">

            <section class="intro">
                <h2>System Overview</h2>
                <p>
                    Welcome to your student management dashboard.
                    Manage student records quickly and efficiently.
                </p>
            </section>

            <section class="stats">

                <div class="card">
                    <div class="stat-label">
                        Total Registered Students
                    </div>

                    <div class="stat-number">
                        <?= (int) $totalStudents ?>
                    </div>

                    <div class="stat-note">
                        Students currently in your database
                    </div>
                </div>

            </section>

            <section class="card">

                <h2 class="section-title">
                    Student Management
                </h2>

                <p class="welcome">
                    Choose an action to continue.
                </p>

                <div class="actions">

                    <div class="action-card">
                        <h3>Register Student</h3>

                        <p>
                            Add a new student and save their
                            information to the database.
                        </p>

                        <a href="add_student.php" class="button">
                            Register Student
                        </a>
                    </div>

                    <div class="action-card">
                        <h3>View Students</h3>

                        <p>
                            View, search, edit, and delete
                            existing student records.
                        </p>

                        <a href="view_students.php" class="button">
                            View Students
                        </a>
                    </div>

                </div>

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