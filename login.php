<?php
// Database connection
$servername = "localhost";
$username = "root"; // Change this if needed
$password = ""; // Change this if needed
$dbname = "crc"; // Change this to your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from the database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Start session and store user information
            session_start();
            $_SESSION['user'] = $user;
            
            // Redirect to welcome page
            header("Location: http://127.0.0.1:5000"); // Adjust this path to your actual welcome page
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that email.";
    }

    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
