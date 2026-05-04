<?php
session_start();
require __DIR__ . "/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";

    if ($username === "" || $password === "") {
        header("Location: login.php?error=1");
        exit();
    }

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
    $user = mysqli_fetch_assoc($result);

    if ($user && isset($user["password"]) && password_verify($password, $user["password"])) {
        $_SESSION["teacher_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];

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
    <?php require __DIR__ . "/navbar_brand.php"; ?>
</header>

<div class="form-container">
    <div class="form-card">
        <h2>Teacher Login</h2>
        <p>Sign in to manage students and marks.</p>
        <?php
        if (isset($_GET['error'])) {
            echo "<p style='color:red; text-align:center;'>Invalid username or password</p>";
        }

        if (isset($_GET['registered'])) {
            echo "<p style='color:green; text-align:center;'>Teacher registered successfully. Please login.</p>";
        }
        ?>
        <form action="login.php" method="POST">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Enter username" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Enter password" required>

            <button type="submit">Login</button>
            <a class="btn btn-secondary" href="register_teacher.php" style="text-align:center;">Register Teacher</a>
        </form>
    </div>
</div>

<footer class="site-footer">
    <p>&copy; 2026 Student Marks System. All rights reserved.</p>
</footer>

</body>
</html>
