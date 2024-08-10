<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root"; // Change this if needed
$password = ""; // Change this if needed
$dbname = "crc"; // Change this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html"); // Redirect to login page if not logged in
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT firstname, lastname, email FROM users WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($firstname, $lastname, $email);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | D-CRC</title>
    <link rel="stylesheet" href="style.css"> <!-- Add CSS if needed -->
</head>
<body>
    <header>
        <h1>Early Detection of Colon Cancer</h1>
        <nav>
            <ul class="header-nav">
                <li><a href="#">Home</a></li>
                <li><a href="#what-is-colon-cancer">About</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php" class="logout">Logout</a></li>
                <li><button class="detect-button" onclick="scrollToForm()">Detect</button></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <div class="content">
            <h2>Profile</h2>
            <p><strong>Firstname:</strong> <?php echo htmlspecialchars($firstname); ?></p>
            <p><strong>Lastname:</strong> <?php echo htmlspecialchars($lastname); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        </div>
    </div>
</body>
</html>
