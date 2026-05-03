<?php
session_start();
if (!isset($_SESSION["teacher_id"])) {
    header("Location: login.php");
    exit();
}

$student_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
if ($student_id <= 0) {
    header("Location: dashboard.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "student_management_system");
if (!$conn) {
    http_response_code(500);
    echo "Database connection failed.";
    exit();
}

$error_message = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $roll_number = isset($_POST["roll_number"]) ? trim($_POST["roll_number"]) : "";
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";

    if ($name === "" || $roll_number === "" || $email === "") {
        $error_message = "All fields are required.";
    } else {
        $duplicate_roll_sql = "SELECT id FROM students WHERE roll_number = ? AND id != ?";
        $duplicate_roll_stmt = mysqli_prepare($conn, $duplicate_roll_sql);

        if (!$duplicate_roll_stmt) {
            $error_message = "Could not update student.";
        } else {
            mysqli_stmt_bind_param($duplicate_roll_stmt, "si", $roll_number, $student_id);
            mysqli_stmt_execute($duplicate_roll_stmt);
            $duplicate_roll_result = mysqli_stmt_get_result($duplicate_roll_stmt);
            $duplicate_roll_row = mysqli_fetch_assoc($duplicate_roll_result);
            mysqli_stmt_close($duplicate_roll_stmt);

            if ($duplicate_roll_row) {
                $error_message = "Roll number already exists";
            } else {
                $duplicate_email_sql = "SELECT id FROM students WHERE email = ? AND id != ?";
                $duplicate_email_stmt = mysqli_prepare($conn, $duplicate_email_sql);

                if (!$duplicate_email_stmt) {
                    $error_message = "Could not update student.";
                } else {
                    mysqli_stmt_bind_param($duplicate_email_stmt, "si", $email, $student_id);
                    mysqli_stmt_execute($duplicate_email_stmt);
                    $duplicate_email_result = mysqli_stmt_get_result($duplicate_email_stmt);
                    $duplicate_email_row = mysqli_fetch_assoc($duplicate_email_result);
                    mysqli_stmt_close($duplicate_email_stmt);

                    if ($duplicate_email_row) {
                        $error_message = "Email already exists";
                    } else {
                        $update_sql = "UPDATE students SET name = ?, roll_number = ?, email = ? WHERE id = ?";
                        $update_stmt = mysqli_prepare($conn, $update_sql);

                        if (!$update_stmt) {
                            $error_message = "Could not update student.";
                        } else {
                            mysqli_stmt_bind_param($update_stmt, "sssi", $name, $roll_number, $email, $student_id);
                            try {
                                $updated = mysqli_stmt_execute($update_stmt);
                            } catch (mysqli_sql_exception $e) {
                                $updated = false;
                                $error_message = "Roll number or email already exists";
                            }
                            mysqli_stmt_close($update_stmt);

                            if ($updated) {
                                mysqli_close($conn);
                                header("Location: dashboard.php");
                                exit();
                            }
                        }

                        if ($error_message === null) {
                            $error_message = "Could not update student.";
                        }
                    }
                }
            }
        }
    }
}

$student_sql = "SELECT id, name, roll_number, email FROM students WHERE id = ?";
$student_stmt = mysqli_prepare($conn, $student_sql);
if (!$student_stmt) {
    mysqli_close($conn);
    http_response_code(500);
    echo "Could not fetch student.";
    exit();
}

mysqli_stmt_bind_param($student_stmt, "i", $student_id);
mysqli_stmt_execute($student_stmt);
$student_result = mysqli_stmt_get_result($student_stmt);
$student = mysqli_fetch_assoc($student_result);
mysqli_stmt_close($student_stmt);

if (!$student) {
    mysqli_close($conn);
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $error_message !== null) {
    $student["name"] = $name;
    $student["roll_number"] = $roll_number;
    $student["email"] = $email;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <?php require __DIR__ . "/navbar_brand.php"; ?>
        <nav class="nav-links">
            <a href="index.html">Home</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="marks.php">Enter Marks</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="form-container">
        <div class="form-card">
            <h2>Edit Student</h2>
            <p>Update student details and save your changes.</p>
            <?php if ($error_message !== null): ?>
                <p style="color:red; text-align:center;"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <label for="name">Name</label>
                <input id="name" type="text" name="name"
                    value="<?php echo htmlspecialchars($student["name"] ?? ""); ?>" required>

                <label for="roll_number">Roll Number</label>
                <input id="roll_number" type="text" name="roll_number"
                    value="<?php echo htmlspecialchars($student["roll_number"] ?? ""); ?>" required>

                <label for="email">Email</label>
                <input id="email" type="email" name="email"
                    value="<?php echo htmlspecialchars($student["email"] ?? ""); ?>" required>

                <button type="submit">Update Student</button>
            </form>
        </div>
    </div>

    <footer class="site-footer">
        <p>&copy; 2026 Student Marks System. All rights reserved.</p>
    </footer>
</body>
</html>
