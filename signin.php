<?php
session_start(); 

$host = "localhost";
$dbname = "task2usersdb"; 
$username = "root"; 
$password = "root"; 

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_input_name = "";
$user_input_pass = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $user_input_name = trim(htmlspecialchars($_POST["username"] ?? ""));
    $user_input_pass = trim(htmlspecialchars($_POST["pass"] ?? ""));

    if (empty($user_input_name)) {
        $errors[] = "Username is required.";
    }
    if (empty($user_input_pass)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $user_input_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            if (password_verify($user_input_pass, $user['password'])) {

                
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['id']; 
                $_SESSION['logged_in'] = true;

                echo "<h3>Welcome, " . $_SESSION['username'] . "!</h3>";
                echo "<p>You are successfully logged in.</p>";

                header("Location: dashboard.php");
                exit;

            } else {
                $errors[] = "Invalid password.";
            }
        } else {
            $errors[] = "User does not exist.";
        }

        mysqli_stmt_close($stmt);
    }
}

// Display errors if any
if (!empty($errors)) {
    echo "<h3>There were errors:</h3>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
}
?>
