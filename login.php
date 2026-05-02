<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo "Method Not Allowed";
    exit();
}

$username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";

if ($username === "" || $password === "") {
    echo "Username and password are required.";
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "student_management_system");

if (!$conn) {
    http_response_code(500);
    echo "Database connection failed.";
    exit();
}

/*  
    ^ statement: the `?` is a placeholder for the username.
    ^ mysqli_prepare()` sets up the query so the value is added safely later (prevents SQL injection).
*/
$sql = "SELECT * FROM teachers WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    mysqli_close($conn);
    http_response_code(500);
    echo "Query preparation failed.";
    exit();
}

/*
    ^ bind_param()` safely inserts the username into the query, and `execute()` runs it.
    ^ get_result()` gets the returned data from the database.
    ^ fetch_assoc()` converts it into an easy-to-use array (`$teacher`).
 */
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$teacher = mysqli_fetch_assoc($result);

/* 
    ^ checks if the teacher is assigned 
    ^ and if teacher password found in the DB
*/
if ($teacher && isset($teacher["password"]) && $teacher["password"] === $password) {

    /*
        ^ These lines store the logged-in teacher’s ID and username in the session.
        ^This lets other pages recognize the user as logged in.
    */
    $_SESSION["teacher_id"] = $teacher["id"];
    $_SESSION["username"] = $teacher["username"];


    /* 
        ^Closes the database statement and connection to free resources.
        ^Then redirects the user to the dashboard and stops the script.
    */
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header("Location: dashboard.php");
    exit();
}

/* 
    ^Closes the statement and database connection after finishing the query.
    ^Then shows an error message because the login credentials are incorrect.

*/
mysqli_stmt_close($stmt);
mysqli_close($conn);
echo "Invalid username or password";
?>
