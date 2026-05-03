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
            align-items: center;
            padding: 24px 16px;
        }

        .search-container {
            width: 100%;
            max-width: 500px;
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
    <h2>Student Marks System</h2>
    <nav class="nav-links">
        <a href="index.html">Home</a>
        <a href="register.html">Register</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="marks.php">Enter Marks</a>
        <a href="result.php">Result</a>
        <a href="login.php">Login</a>
    </nav>
</header>

<main class="result-main">
    <div class="search-container">
        <form method="GET" action="result.php">
            <div class="search-box">
                <input type="text" name="query" placeholder="Search students by name..." required>
                <button type="submit">Search</button>
            </div>
        </form>
    </div>
</main>

<footer class="site-footer">
    <p>&copy; 2026 Student Marks System. All rights reserved.</p>
</footer>
</body>
</html>
