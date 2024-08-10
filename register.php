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
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

    // Insert user into the database
    $sql = "INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $firstname, $lastname, $email, $password);

    if ($stmt->execute()) {
        // Redirect to login page
        header("Location: index.html"); // Adjust this path to your actual login page
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
