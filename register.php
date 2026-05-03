<?php
session_start();

$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "student_management_system";

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

if (!$conn) {
    header("Location: register.html?error=db");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $roll = isset($_POST["roll_number"]) ? trim($_POST["roll_number"]) : "";
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";

    if ($name === "" || $roll === "" || $email === "") {
        header("Location: register.html?error=1");
        exit();
    }

    $check_sql = "SELECT id FROM students WHERE roll_number = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);

    if (!$check_stmt) {
        header("Location: register.html?error=db");
        exit();
    }

    mysqli_stmt_bind_param($check_stmt, "s", $roll);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        mysqli_stmt_close($check_stmt);
        header("Location: register.html?error=exists");
        exit();
    }

    mysqli_stmt_close($check_stmt);

    $insert_sql = "INSERT INTO students (name, roll_number, email) VALUES (?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);

    if (!$insert_stmt) {
        header("Location: register.html?error=db");
        exit();
    }

    mysqli_stmt_bind_param($insert_stmt, "sss", $name, $roll, $email);
    $insert_ok = mysqli_stmt_execute($insert_stmt);
    mysqli_stmt_close($insert_stmt);

    if ($insert_ok) {
        header("Location: dashboard.php?success=student_added");
        exit();
    }

    header("Location: register.html?error=db");
    exit();
}

header("Location: register.html");
exit();
?>
