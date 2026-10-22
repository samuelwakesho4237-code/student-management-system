```php
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$search = trim($_GET['search'] ?? '');

$sql = "SELECT std_id, full_name, email, course, age, created_at
        FROM students
        WHERE full_name LIKE ?
           OR email LIKE ?
           OR course LIKE ?
        ORDER BY std_id DESC";

$stmt = $conn->prepare($sql);

$term = "%" . $search . "%";
$stmt->bind_param("sss", $term, $term, $term);
$stmt->execute();

$result = $stmt->get_result();
$totalStudents = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>View Students | Student Management System</title>

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
            max-width: 1500px;
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

        .stats {
            display: grid;
            grid-template-columns: repeat(
                auto-fit,
                minmax(200px, 1fr)
            );
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 22px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 15px #0f172a0a;
        }

        .stat-label {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .stat-number {
            color: #1d4ed8;
            font-size: 32px;
            font-weight: bold;
        }

        .panel {
            background: white;
            padding: 25px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 15px #0f172a0a;
        }

        .panel-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 22px;
        }

        .panel-heading h3 {
            margin: 0 0 7px;
            font-size: 20px;
        }

        .panel-heading p {
            color: #64748b;
            margin: 0;
            font-size: 14px;
        }

        .button {
            display: inline-block;
            padding: 10px 14px;
            border: none;
            border-radius: 7px;
            background: #1d4ed8;
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            white-space: nowrap;
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

        .delete-button {
            background: #b91c1c;
        }

        .delete-button:hover {
            background: #991b1b;
        }

        .search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-form input {
            flex: 1;
            min-width: 200px;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
        }

        .search-form input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px #2563eb20;
        }

        .table-container {
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th,
        td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }

        th {
            background: #172554;
            color: white;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        tbody tr:hover {
            background: #eff6ff;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .student-name {
            font-weight: bold;
            color: #1e293b;
        }

        .action-cell {
            white-space: nowrap;
        }

        .action-cell form {
            display: inline;
        }

        .action-cell .button {
            margin: 2px;
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

        .empty {
            padding: 35px 15px;
            text-align: center;
            color: #64748b;
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

            .panel {
                padding: 16px;
            }

            .panel-heading {
                align-items: flex-start;
                flex-direction: column;
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

        <a href="index.php" class="nav-link">
            Dashboard
        </a>

        <a href="add_student.php" class="nav-link">
            Register Student
        </a>

        <a href="view_students.php" class="nav-link active">
            View Students
        </a>

        <a href="logout.php" class="nav-link logout">
            Logout
        </a>

    </aside>

    <div class="main">

        <header class="topbar">
            <h1>Registered Students</h1>

            <div class="welcome">
                Welcome,
                <strong>
                    <?= htmlspecialchars($_SESSION["username"]) ?>
                </strong>
            </div>
        </header>

        <main class="content">

            <section class="intro">
                <h2>Student Records</h2>
                <p>
                    Search, review, edit, and manage registered students.
                </p>
            </section>

            <section class="stats">
                <div class="stat-card">
                    <div class="stat-label">
                        Students Found
                    </div>

                    <div class="stat-number">
                        <?= (int)$totalStudents ?>
                    </div>
                </div>
            </section>

            <?php if (isset($_GET['updated']) && $_GET['updated'] === '1'): ?>
                <div class="message success">
                    Student updated successfully.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['deleted']) && $_GET['deleted'] === '1'): ?>
                <div class="message success">
                    Student deleted successfully.
                </div>
            <?php endif; ?>

            <section class="panel">

                <div class="panel-heading">
                    <div>
                        <h3>All Student Records</h3>
                        <p>
                            Find a student using their name, email, or course.
                        </p>
                    </div>

                    <a href="add_student.php" class="button">
                        + Register Student
                    </a>
                </div>

                <form method="GET"
                      action="view_students.php"
                      class="search-form">

                    <input
                        type="search"
                        name="search"
                        placeholder="Search name, email, or course..."
                        value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                    >

                    <button type="submit" class="button">
                        Search
                    </button>

                    <a href="view_students.php"
                       class="button button-secondary">
                        Clear
                    </a>

                </form>

                <?php if ($totalStudents > 0): ?>

                    <div class="table-container">

                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Course</th>
                                    <th>Age</th>
                                    <th>Date Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php while ($student = $result->fetch_assoc()): ?>

                                    <tr>
                                        <td>
                                            <?= (int)$student['std_id'] ?>
                                        </td>

                                        <td class="student-name">
                                            <?= htmlspecialchars(
                                                $student['full_name'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $student['email'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $student['course'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= $student['age'] === null
                                                ? 'Not provided'
                                                : (int)$student['age'] ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $student['created_at'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td class="action-cell">

                                            <a
                                                class="button"
                                                href="edit_student.php?id=<?= (int)$student['std_id'] ?>"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                action="delete_student.php"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this student?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int)$student['std_id'] ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="button delete-button"
                                                >
                                                    Delete
                                                </button>

                                            </form>

                                        </td>
                                    </tr>

                                <?php endwhile; ?>

                            </tbody>
                        </table>

                    </div>

                <?php else: ?>

                    <div class="empty">
                        <h3>No students found</h3>

                        <p>
                            <?= $search !== ''
                                ? 'No students match your search. Try another name, email, or course.'
                                : 'There are no registered students yet. Register your first student to get started.' ?>
                        </p>

                        <?php if ($search === ''): ?>
                            <a href="add_student.php" class="button">
                                Register First Student
                            </a>
                        <?php endif; ?>
                    </div>

                <?php endif; ?>

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