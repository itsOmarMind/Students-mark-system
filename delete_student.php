<?php
session_start();
require __DIR__ . "/database.php";
if (!isset($_SESSION["teacher_id"])) {
    header("Location: login.php");
    exit();
}

$student_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
if ($student_id <= 0) {
    header("Location: dashboard.php");
    exit();
}
if (!$conn) {
    http_response_code(500);
    echo "Database connection failed.";
    exit();
}

$delete_sql = "DELETE FROM students WHERE id = ?";
$delete_stmt = mysqli_prepare($conn, $delete_sql);
if ($delete_stmt) {
    mysqli_stmt_bind_param($delete_stmt, "i", $student_id);
    mysqli_stmt_execute($delete_stmt);
    mysqli_stmt_close($delete_stmt);
}

mysqli_close($conn);
header("Location: dashboard.php");
exit();
?>
