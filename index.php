<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost"; 
$user = "root"; 
$pass = "";
$db   = "personalised_learning";


$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $cpassword = trim($_POST['cpassword']);

    
    if ($password !== $cpassword) {
        die("Passwords do not match.");
    }

    
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        die("This email is already registered. Try logging in.");
    }
    $checkStmt->close();

    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

   
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "Registration successful! <a href='login.html'>Login now</a>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
