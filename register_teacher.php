<?php
session_start();
require __DIR__ . "/database.php";

$username = "";
$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $confirm_password = isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : "";

    if ($username == "") {
        $error_message = "Username is required.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters.";
    } elseif ($password != $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        if (!$conn) {
            $error_message = "Database connection failed.";
        } else {
            // Check if username already exists
            $check_sql = "SELECT id FROM teachers WHERE username = ?";
            $check_stmt = mysqli_prepare($conn, $check_sql);

            if (!$check_stmt) {
                $error_message = "Could not check username.";
            } else {
                mysqli_stmt_bind_param($check_stmt, "s", $username);
                mysqli_stmt_execute($check_stmt);
                mysqli_stmt_store_result($check_stmt);

                if (mysqli_stmt_num_rows($check_stmt) > 0) {
                    $error_message = "Username already exists.";
                } else {
                    // Hash password before saving
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    $insert_sql = "INSERT INTO teachers (username, password) VALUES (?, ?)";
                    $insert_stmt = mysqli_prepare($conn, $insert_sql);

                    if (!$insert_stmt) {
                        $error_message = "Could not register teacher.";
                    } else {
                        mysqli_stmt_bind_param($insert_stmt, "ss", $username, $hashed_password);

                        if (mysqli_stmt_execute($insert_stmt)) {
                            $success_message = "Teacher registered successfully.";
                            $username = "";
                        } else {
                            $error_message = "Could not register teacher.";
                        }

                        mysqli_stmt_close($insert_stmt);
                    }
                }

                mysqli_stmt_close($check_stmt);
            }

            mysqli_close($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Teacher</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="navbar">
    <?php require __DIR__ . "/navbar_brand.php"; ?>
</header>

<div class="form-container">
    <div class="form-card">
        <h2>Register Teacher</h2>
        <p>Create a teacher account to manage students and marks.</p>

        <?php
        if ($error_message != "") {
            echo "<p style='color:red; text-align:center;'>" . htmlspecialchars($error_message) . "</p>";
        }

        if ($success_message != "") {
            echo "<p style='color:green; text-align:center;'>" . htmlspecialchars($success_message) . "</p>";
            echo "<p style='text-align:center;'><a href='login.php'>Go to Login</a></p>";
        }
        ?>

        <form action="register_teacher.php" method="POST">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Enter username" required
                value="<?php echo htmlspecialchars($username); ?>">

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Enter password" required>

            <label for="confirm_password">Confirm Password</label>
            <input id="confirm_password" type="password" name="confirm_password" placeholder="Confirm password" required>

            <button type="submit">Register Teacher</button>
        </form>
    </div>
</div>

<footer class="site-footer">
    <p>&copy; 2026 Student Marks System. All rights reserved.</p>
</footer>

</body>
</html>
