<?php
session_start();
header('Content-Type: application/json');

// debug input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

echo json_encode([
    'success' => false,
    'message' => 'Debugging input data',
    'raw_input' => $input,
    'decoded' => $data
]);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_accounts";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['won']) || !isset($data['timePlayed'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit();
}

$won = filter_var($data['won'], FILTER_VALIDATE_BOOLEAN);
$timePlayed = filter_var($data['timePlayed'], FILTER_VALIDATE_INT);

$checkStatsStmt = $conn->prepare("SELECT 1 FROM game_stats WHERE user_id = ?");
$checkStatsStmt->bind_param("i", $user_id);
$checkStatsStmt->execute();
$checkStatsStmt->store_result();

if ($checkStatsStmt->num_rows === 0) {
    $insertStatsStmt = $conn->prepare("INSERT INTO game_stats (user_id, games_played, games_won, time_played) VALUES (?, 0, 0, 0)");
    $insertStatsStmt->bind_param("i", $user_id);
    $insertStatsStmt->execute();
    $insertStatsStmt->close();
}
$checkStatsStmt->close();

$sql = "UPDATE game_stats SET games_played = games_played + 1, games_won = games_won + ?, time_played = time_played + ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$gamesWonIncrement = $won ? 1 : 0;
$stmt->bind_param("iii", $gamesWonIncrement, $timePlayed, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Stats updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update stats.']);
}

$stmt->close();
$conn->close();
?>
