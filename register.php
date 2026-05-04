<?php
session_start();

// Database connection
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "student_management_system";

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

// Variables for messages and form values
$error_message = "";
$success_message = "";
$name = "";
$roll = "";
$email = "";

if (!$conn) {
    $error_message = "Database connection failed.";
}

// Form processing
if ($_SERVER["REQUEST_METHOD"] === "POST" && $conn) {
    $name = trim($_POST["name"]);
    $roll = trim($_POST["roll_number"]);
    $email = trim($_POST["email"]);

    // Simple validation
    if ($name == "" || $roll == "" || $email == "") {
        $error_message = "All fields are required.";
    } else {
        // Check if roll number already exists
        $check_sql = "SELECT id FROM students WHERE roll_number = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);

        if (!$check_stmt) {
            $error_message = "Could not check student.";
        } else {
            mysqli_stmt_bind_param($check_stmt, "s", $roll);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);

            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $error_message = "Roll number already exists.";
            } else {
                // Insert new student
                $insert_sql = "INSERT INTO students (name, roll_number, email) VALUES (?, ?, ?)";
                $insert_stmt = mysqli_prepare($conn, $insert_sql);

                if (!$insert_stmt) {
                    $error_message = "Could not register student.";
                } else {
                    mysqli_stmt_bind_param($insert_stmt, "sss", $name, $roll, $email);

                    if (mysqli_stmt_execute($insert_stmt)) {
                        $success_message = "Student registered successfully.";
                        $name = "";
                        $roll = "";
                        $email = "";
                    } else {
                        $error_message = "Could not register student.";
                    }

                    mysqli_stmt_close($insert_stmt);
                }
            }

            mysqli_stmt_close($check_stmt);
        }
    }
}

if ($conn) {
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="navbar">
    <?php require __DIR__ . "/navbar_brand.php"; ?>
</header>

<div class="form-container">
    <div class="form-card">
        <h2>Register New Student</h2>
        <p>Add student information to create a new record.</p>

        <?php
        // Show error or success message
        if ($error_message != "") {
            echo "<p style='color:red; text-align:center;'>" . htmlspecialchars($error_message) . "</p>";
        }

        if ($success_message != "") {
            echo "<p style='color:green; text-align:center;'>" . htmlspecialchars($success_message) . "</p>";
        }
        ?>

        <form action="register.php" method="POST" id="registerForm">
            <label for="name">Full Name</label>
            <input id="name" type="text" name="name" placeholder="Enter full name" required
                value="<?php echo htmlspecialchars($name); ?>">

            <label for="roll">Roll Number</label>
            <input id="roll" type="text" name="roll_number" placeholder="e.g. 101" required
                value="<?php echo htmlspecialchars($roll); ?>">

            <label for="email">Email Address</label>
            <input id="email" type="email" name="email" placeholder="student@example.com" required
                value="<?php echo htmlspecialchars($email); ?>">

            <button type="submit">Register Student</button>
        </form>
    </div>
</div>

<footer class="site-footer">
    <p>&copy; 2026 Student Marks System. All rights reserved.</p>
</footer>

</body>
</html>
