<?php
session_start();
require __DIR__ . "/database.php";

$query = "";
$results = [];
$searched = false;
$error_message = "";

function getGrade($mark)
{
    if ($mark >= 95) {
        return "A+";
    } elseif ($mark >= 90) {
        return "A";
    } elseif ($mark >= 85) {
        return "B+";
    } elseif ($mark >= 80) {
        return "B";
    } elseif ($mark >= 75) {
        return "C+";
    } elseif ($mark >= 70) {
        return "C";
    } elseif ($mark >= 65) {
        return "D+";
    } elseif ($mark >= 60) {
        return "D";
    } else {
        return "F";
    }
}

// Check if the user searched
if (isset($_GET["query"])) {
    $query = trim($_GET["query"]);

    if ($query != "") {
        $searched = true;

        if (!$conn) {
            $error_message = "Database connection failed.";
        } else {
            // Search students by name and get their marks and courses
            $sql = "SELECT students.name, courses.course_name, marks.mark
                    FROM students
                    INNER JOIN marks ON students.id = marks.student_id
                    INNER JOIN courses ON courses.id = marks.course_id
                    WHERE students.name LIKE ?";

            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                $error_message = "Search failed.";
            } else {
                $search_name = "%" . $query . "%";

                mysqli_stmt_bind_param($stmt, "s", $search_name);
                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);

                while ($row = mysqli_fetch_assoc($result)) {
                    $results[] = $row;
                }

                mysqli_stmt_close($stmt);
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
    <title>Student Result</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .result-main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 24px 16px;
        }

        .search-container {
            width: 100%;
            max-width: 900px;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border-radius: 16px;
            padding: 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        }

        .search-box input[type="text"] {
            flex: 1;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            height: 52px;
            padding: 0 16px;
            font-size: 1rem;
            outline: none;
        }

        .search-box input[type="text"]:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .search-box button {
            height: 52px;
            padding: 0 24px;
            border: 0;
            border-radius: 12px;
            background: #2563eb;
            color: #fff;
            font-size: 0.95rem;
            cursor: pointer;
        }

        .search-box button:hover {
            background: #1d4ed8;
        }

        .result-table {
            margin-top: 24px;
        }

        @media (max-width: 600px) {
            .search-box {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<header class="navbar">
    <?php require __DIR__ . "/navbar_brand.php"; ?>
</header>

<main class="result-main">
    <div class="search-container">
        <form method="GET" action="result.php">
            <div class="search-box">
                <input type="text" name="query" placeholder="Search students by name..." required
                    value="<?php echo htmlspecialchars($query); ?>">
                <button type="submit">Search</button>
            </div>
        </form>

        <div class="result-table">
            <?php
            if ($error_message != "") {
                echo "<p style='color:red; text-align:center;'>" . htmlspecialchars($error_message) . "</p>";
            } elseif ($searched && empty($results)) {
                echo "<p style='text-align:center;'>No results found</p>";
            } elseif (!empty($results)) {
                echo "<table>";
                echo "<tr>";
                echo "<th>Student Name</th>";
                echo "<th>Course Name</th>";
                echo "<th>Mark</th>";
                echo "<th>Grade</th>";
                echo "</tr>";

                foreach ($results as $row) {
                    $grade = getGrade($row["mark"]);

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["course_name"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["mark"]) . "</td>";
                    echo "<td>" . htmlspecialchars($grade) . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            }
            ?>
        </div>
    </div>
</main>

<footer class="site-footer">
    <p>&copy; 2026 Student Marks System. All rights reserved.</p>
</footer>
</body>
</html>
