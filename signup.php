<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_accounts";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = htmlspecialchars(trim($_POST['fname']));
    $lastName = htmlspecialchars(trim($_POST['lname']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirmPassword = htmlspecialchars(trim($_POST['cfrmpassword']));

    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.'); window.location.href = 'signup.html';</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.location.href = 'signup.html';</script>";
        exit();
    }

    $checkEmailStmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();

    if ($checkEmailStmt->num_rows > 0) {
        echo "<script>alert('This email is already registered.'); window.location.href = 'signup.html';</script>";
        $checkEmailStmt->close();
        $conn->close();
        exit();
    }
    $checkEmailStmt->close();

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);

    if ($stmt->execute()) {
        $userId = $stmt->insert_id;
        $gameStatsStmt = $conn->prepare("INSERT INTO game_stats (user_id, games_played, games_won, time_played) VALUES (?, 0, 0, 0)");
        $gameStatsStmt->bind_param("i", $userId);
        $gameStatsStmt->execute();
        $gameStatsStmt->close();

        echo "<script>alert('Account created successfully!'); window.location.href = 'login.html';</script>";
    } else {
        echo "<script>alert('Database error: " . $stmt->error . "'); window.location.href = 'signup.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
