<?php
session_start();
require __DIR__ . "/database.php";

if (!isset($_SESSION["teacher_id"])) {
    header("Location: login.php");
    exit();
}

$teacher_id = (int) $_SESSION["teacher_id"];

$error_message = null;
$form_roll = "";
$form_course_name = "";
$form_course_mark = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!$conn) {
        $error_message = "Database connection failed.";
    } else {
        $roll = isset($_POST["roll"]) ? trim($_POST["roll"]) : "";
        $course_name = isset($_POST["course_name"]) ? trim($_POST["course_name"]) : "";
        $course_mark = isset($_POST["course_mark"]) ? $_POST["course_mark"] : "";

        $form_roll = $roll;
        $form_course_name = $course_name;
        $form_course_mark = is_scalar($course_mark) ? (string) $course_mark : "";

        if ($roll === "") {
            $error_message = "Roll number is required.";
        } elseif ($course_name === "") {
            $error_message = "Course name cannot be empty.";
        } elseif (!is_numeric($course_mark)) {
            $error_message = "Mark must be a number.";
        } else {
            $mark_val = (float) $course_mark;
            if ($mark_val < 0 || $mark_val > 100) {
                $error_message = "Mark must be between 0 and 100.";
            }
        }

        if ($error_message !== null) {
            mysqli_close($conn);
        } else {
            $student_sql = "SELECT id FROM students WHERE roll_number = ?";
            $student_stmt = mysqli_prepare($conn, $student_sql);

            if (!$student_stmt) {
                $error_message = "Unable to verify student.";
                mysqli_close($conn);
            } else {
                mysqli_stmt_bind_param($student_stmt, "s", $roll);
                mysqli_stmt_execute($student_stmt);
                $student_result = mysqli_stmt_get_result($student_stmt);
                $student_row = mysqli_fetch_assoc($student_result);
                mysqli_stmt_close($student_stmt);

                if (!$student_row || !isset($student_row["id"])) {
                    $error_message = "Student not found";
                    mysqli_close($conn);
                } else {
                    $student_id = (int) $student_row["id"];

                    mysqli_begin_transaction($conn);
                    $tx_ok = true;

                    $course_find = "SELECT id FROM courses WHERE course_name = ? AND teacher_id = ?";
                    $stmt_find = mysqli_prepare($conn, $course_find);

                    if (!$stmt_find) {
                        $tx_ok = false;
                    } else {
                        mysqli_stmt_bind_param($stmt_find, "si", $course_name, $teacher_id);
                        mysqli_stmt_execute($stmt_find);
                        $course_res = mysqli_stmt_get_result($stmt_find);
                        $course_row = mysqli_fetch_assoc($course_res);
                        mysqli_stmt_close($stmt_find);

                        if ($course_row && isset($course_row["id"])) {
                            $course_id = (int) $course_row["id"];
                        } else {
                            $course_ins = "INSERT INTO courses (course_name, teacher_id) VALUES (?, ?)";
                            $stmt_ins = mysqli_prepare($conn, $course_ins);

                            if (!$stmt_ins) {
                                $tx_ok = false;
                            } else {
                                mysqli_stmt_bind_param($stmt_ins, "si", $course_name, $teacher_id);

                                if (!mysqli_stmt_execute($stmt_ins)) {
                                    mysqli_stmt_close($stmt_ins);
                                    $tx_ok = false;
                                } else {
                                    $course_id = (int) mysqli_insert_id($conn);
                                    mysqli_stmt_close($stmt_ins);
                                }
                            }
                        }
                    }

                    if ($tx_ok) {
                        $mark_ins = "INSERT INTO marks (student_id, course_id, teacher_id, mark) VALUES (?, ?, ?, ?)";
                        $stmt_mark = mysqli_prepare($conn, $mark_ins);

                        if (!$stmt_mark) {
                            $tx_ok = false;
                        } else {
                            mysqli_stmt_bind_param(
                                $stmt_mark,
                                "iiid",
                                $student_id,
                                $course_id,
                                $teacher_id,
                                $mark_val
                            );
                            if (!mysqli_stmt_execute($stmt_mark)) {
                                $tx_ok = false;
                            }
                            mysqli_stmt_close($stmt_mark);
                        }
                    }

                    if ($tx_ok) {
                        mysqli_commit($conn);
                        mysqli_close($conn);
                        header("Location: result.php");
                        exit();
                    }

                    mysqli_rollback($conn);
                    $error_message = "Could not save marks. Please try again.";
                    mysqli_close($conn);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Marks</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="navbar">
        <?php require __DIR__ . "/navbar_brand.php"; ?>
    </header>

    <div class="form-container">
        <div class="form-card">
            <h2>Enter Student Marks</h2>
            <p>Add marks quickly using the form below.</p>
            <?php if (isset($_GET["success"]) && (string) $_GET["success"] === "1"): ?>
                <p style="color:green; text-align:center;">Marks saved successfully.</p>
            <?php endif; ?>
            <?php if ($error_message !== null): ?>
                <p style="color:red; text-align:center;"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <form action="" method="POST">
                <label for="roll">Roll Number</label>
                <input id="roll" type="text" name="roll" placeholder="e.g. 101" required
                    value="<?php echo htmlspecialchars($form_roll); ?>">

                <div class="course-row">
                    <div class="course-field">
                        <label for="course_name">Course Name</label>
                        <input id="course_name" type="text" name="course_name" placeholder="Enter course name"
                            required value="<?php echo htmlspecialchars($form_course_name); ?>">
                    </div>
                    <div class="course-field">
                        <label for="course_mark">Mark</label>
                        <input id="course_mark" type="number" name="course_mark" placeholder="0 - 100" min="0"
                            max="100" required value="<?php echo htmlspecialchars($form_course_mark); ?>">
                    </div>
                </div>

                <button type="submit">Save Marks</button>
            </form>
        </div>
    </div>

    <footer class="site-footer">
        <p>&copy; 2026 Student Marks System. All rights reserved.</p>
    </footer>
</body>

</html>
