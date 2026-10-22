
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: view_students.php");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: view_students.php?error=invalid");
    exit;
}

$stmt = $conn->prepare("DELETE FROM students WHERE std_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$deleted = $stmt->affected_rows > 0;
$stmt->close();

header("Location: view_students.php?deleted=" . ($deleted ? "1" : "0"));
exit;
?>
