<?php
session_start();
require __DIR__ . "/database.php";
if (!isset($_SESSION["teacher_id"])) {
    header("Location: login.php");
    exit();
}
if (!$conn) {
    http_response_code(500);
    echo "Database connection failed.";
    exit();
}

$students = [];
$searchRoll = isset($_GET["roll"]) ? trim($_GET["roll"]) : "";

if ($searchRoll !== "") {
    $sql = "SELECT * FROM students WHERE roll_number = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $searchRoll);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
} else {
    $sql = "SELECT * FROM students";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
        mysqli_free_result($result);
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="navbar">

    <!--
        ^ 'requrie' Includes another PHP file.

        ^ __DIR__ gives the absolute path of the current file’s folder,
        ^ so PHP can reliably locate navbar_brand.php regardless of where 
        ^ the script is called from.
    -->
    
    <?php require __DIR__ . "/navbar_brand.php"; ?>
    </header>

    <div class="container">
        <div class="card">
            <h2>Student Dashboard</h2>
            <p>View and update student records.</p>
            <form method="GET">
                <input type="text" name="roll" placeholder="Search by Roll Number"
                    value="<?php echo htmlspecialchars($searchRoll); ?>">
                <button type="submit">Search</button>
            </form>
            <table>
                <tr>
                    <th>Roll No</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student["roll_number"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($student["name"] ?? ""); ?></td>
                            <td class="actions">
                                <a href="edit_student.php?id=<?php echo (int) ($student['id'] ?? 0); ?>"
                                    onclick="return confirm('Do you want to edit this student?');">
                                    <button>Edit</button>
                                </a>
                                <a href="delete_student.php?id=<?php echo (int) ($student['id'] ?? 0); ?>"
                                    onclick="return confirm('Are you sure you want to delete this student?');">
                                    <button class="btn-danger">Delete</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No student found</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <footer class="site-footer">
        <p>&copy; 2026 Student Marks System. All rights reserved.</p>
    </footer>

</body>

</html>
