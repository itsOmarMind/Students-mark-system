<?php
session_start();
if (!isset($_SESSION["teacher_id"])) {
    header("Location: login.html");
    exit();
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
        <h2>Student Marks System</h2>
        <nav class="nav-links">
            <a href="index.html">Home</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="marks.php">Enter Marks</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="form-container">
        <div class="form-card">
            <h2>Enter Student Marks</h2>
            <p>Add marks quickly using the form below.</p>
            <form action="marks.php" method="POST">
                <label for="roll">Roll Number</label>
                <input id="roll" type="text" name="roll" placeholder="e.g. 101" required>

                <div class="course-row">
                    <div class="course-field">
                        <label for="course_name">Course Name</label>
                        <input id="course_name" type="text" name="course_names[]" placeholder="Enter course name"
                            required>
                    </div>
                    <div class="course-field">
                        <label for="course_mark">Mark</label>
                        <input id="course_mark" type="number" name="course_marks[]" placeholder="0 - 100" min="0"
                            max="100" required>
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
