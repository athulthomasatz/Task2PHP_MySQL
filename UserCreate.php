<?php
$name=$pass="";
$errors=[];

$host = "localhost";
$dbname = "task2usersdb";
$username = "root";
$password = "root";

$conn = mysqli_connect($host,$username,$password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname";

$sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname";

if (mysqli_query($conn, $sql_create_db)) {
    echo "Database '$dbname' created or already exists.<br>";
} else {
    echo "Error creating database: " . mysqli_error($conn);
}

// Select the database
mysqli_select_db($conn, $dbname);

// Create table if it doesn't exist
$table_creation_query = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL
    )
";

if (mysqli_query($conn, $table_creation_query)) {
    echo "Table 'users' created or already exists.<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
// form validation

if($_SERVER["REQUEST_METHOD"]==="POST")
{
    $name = trim(htmlspecialchars($_POST["username"]?? ""));
    $raw_pass = trim($_POST["pass"] ?? ""); 
    // $pass = password_hash($raw_pass, PASSWORD_DEFAULT); 

    if(empty($name)){
        $errors[]="Name is Required.";
    }
    if(strlen($name)<3){
        $errors [] =  "Minimum 3 words Required for Name ";
    }
    if (empty($raw_pass)) {
        $errors[] = "password is required.";
    }
    if(strlen($raw_pass)<6){
        $errors [] = "Minimum 6 Characters required for strong Password";
    }
}
if (empty($errors)) {
        $pass = password_hash($raw_pass, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $name, $pass);
        // echo "<h3>Form submitted successfully!</h3>";


        if (mysqli_stmt_execute($stmt)) {
            echo "<h3>Form submitted successfully!</h3>";
            echo "<p><strong>Name:</strong> $name</p>";
            echo "<p><strong>Password:</strong> <i>Stored securely</i></p>";
            echo "<p><i>Account successfully created and stored in DB</i></p>";
            echo '<a href="signin.html">Sign In</a>';
        } else {
            echo "Error inserting data: " . mysqli_error($conn);
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);
    } else {
        echo "<h3>There were errors:</h3>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
    }

    if (!empty($errors)) {
    echo "<h3>There were errors:</h3>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
}
?>
