<?php
session_start();

// database configuration
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "user_accounts";

// create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// go to login if user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

// get leaderboard data (top players sorted by games won)
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'games_won';
$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';

$sql = "SELECT u.first_name, u.last_name, g.games_played, g.games_won, g.time_played 
        FROM users u 
        JOIN game_stats g ON u.id = g.user_id
        ORDER BY $sortColumn $order 
        LIMIT 10"; // top 10 players
$result = $conn->query($sql);

// get the games played by the logged-in user
$userEmail = $_SESSION['user'];
$userGames = $conn->prepare("SELECT g.games_played, g.games_won, g.time_played 
                             FROM users u 
                             JOIN game_stats g ON u.id = g.user_id 
                             WHERE u.email = ?");
$userGames->bind_param("s", $userEmail);
$userGames->execute();
$userGamesResult = $userGames->get_result();
$userData = $userGamesResult->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Leaderboard </title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="menu_style.css">
</head>
<body>

    <div class="navigation_bar">

        <!-- home button (go to main menu)-->
        <button class="nav_button" onclick="window.location.href='index.php';">Home</button>

        <!-- logout button -->
        <form action="logout.php" method="post">
            <button class="nav_button" type="submit">Logout</button>
        </form>

    </div>

    <h1 class="leaderboard_header">Leaderboard</h1>

    <div class="form_container">

        <!-- sorting options -->
        <form method="get" action="leaderboard.php">

            <label for="sort">Sort By:</label>
            <select name="sort" id="sort">
                <option value="games_won">Games Won</option>
                <option value="games_played">Games Played</option>
                <option value="time_played">Time Played</option>
            </select>

            <label for="order">Order:</label>
            <select name="order" id="order">
                <option value="desc">Descending</option>
                <option value="asc">Ascending</option>
            </select>

            <button class="sort" type="submit">Sort</button>

        </form>

        <!-- top players table -->
        <table border="1">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Games Played</th>
                    <th>Games Won</th>
                    <th>Time Played (minutes)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>
                                <td>" . $row['games_played'] . "</td>
                                <td>" . $row['games_won'] . "</td>
                                <td>" . round($row['time_played'] / 60, 2) . "</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No data available</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- logged in user stats -->
        <h3>Your Stats</h3>
        <?php if ($userData): ?>
            <p>Games Played: <?php echo $userData['games_played']; ?></p>
            <p>Games Won: <?php echo $userData['games_won']; ?></p>
            <p>Time Played: <?php echo round($userData['time_played'] / 60, 2); ?> minutes</p>
        <?php else: ?>
            <p>No stats available for your account</p>
        <?php endif; ?>

</body>
<script>
    
</script>
</html>