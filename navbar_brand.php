<?php
// Start the session if it has not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<h2>
    Student Marks System
    <?php if (isset($_SESSION["username"])): ?>
        <span style="font-size:14px; margin-left:10px; color:#ccc;">
            Hey, <?php echo htmlspecialchars($_SESSION["username"]); ?>
        </span>
    <?php endif; ?>
</h2>

<nav class="nav-links">
    <?php
    // Check if the user is logged in
    if (isset($_SESSION["username"])) {
        echo '<a href="index.html">Home</a>';
        echo '<a href="dashboard.php">Dashboard</a>';
        echo '<a href="marks.php">Enter Marks</a>';
        echo '<a href="result.php">Result</a>';
        echo '<a href="logout.php">Logout</a>';
    } else {
        echo '<a href="index.html">Home</a>';
        echo '<a href="register.php">Register</a>';
        echo '<a href="login.php">Login</a>';
    }
    ?>
</nav>
