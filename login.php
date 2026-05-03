<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";

    if ($username === "" || $password === "") {
        header("Location: login.php?error=1");
        exit();
    }

    $conn = mysqli_connect("localhost", "root", "", "student_management_system");

    if (!$conn) {
        http_response_code(500);
        echo "Database connection failed.";
        exit();
    }

    $sql = "SELECT * FROM teachers WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        mysqli_close($conn);
        http_response_code(500);
        echo "Query preparation failed.";
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $teacher = mysqli_fetch_assoc($result);

    if ($teacher && isset($teacher["password"]) && $teacher["password"] === $password) {
        $_SESSION["teacher_id"] = $teacher["id"];
        $_SESSION["username"] = $teacher["username"];

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        header("Location: dashboard.php");
        exit();
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: login.php?error=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="navbar">
    <h2>Student Marks System</h2>
    <nav class="nav-links">
        <a href="index.html">Home</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="marks.php">Enter Marks</a>
        <a href="login.php">Login</a>
    </nav>
</header>

<div class="form-container">
    <div class="form-card">
        <h2>Teacher Login</h2>
        <p>Sign in to manage students and marks.</p>
        <?php
        if (isset($_GET['error'])) {
            echo "<p style='color:red; text-align:center;'>Invalid username or password</p>";
        }
        ?>
        <form action="login.php" method="POST">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Enter username" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Enter password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</div>

<footer class="site-footer">
    <p>&copy; 2026 Student Marks System. All rights reserved.</p>
</footer>

</body>
</html>
