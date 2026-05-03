<h2>
    Student Marks System
    <?php if (isset($_SESSION["username"])): ?>
        <span style="font-size:14px; margin-left:10px; color:#ccc;">
            Hey, <?php echo htmlspecialchars($_SESSION["username"]); ?>
        </span>
    <?php endif; ?>
</h2>
