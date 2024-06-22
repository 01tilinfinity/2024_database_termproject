<?php
session_start();
include 'db.php';
include 'header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error_message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['query'])) {
    $query = $_GET['query'];

    try {
        
        $sql = "SELECT Artists.artist_id, Users.username, COUNT(Tracks.track_id) AS track_count
                FROM Artists
                JOIN Users ON Artists.user_id = Users.user_id
                LEFT JOIN Tracks ON Tracks.artist_id = Artists.artist_id
                WHERE Users.username LIKE :query
                GROUP BY Artists.artist_id, Users.username";
        $stmt = $conn->prepare($sql);
        $search_query = '%' . $query . '%';
        $stmt->bindParam(':query', $search_query, PDO::PARAM_STR);
        $stmt->execute();
        $artists = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = '검색 중 오류가 발생했습니다: ' . $e->getMessage();
    }
}
?>

<div class="container">
    <h1>Search Artists</h1>
    <form method="GET" action="search_artists.php">
        <input type="text" name="query" placeholder="Search Artists" required>
        <input type="submit" value="Search">
    </form>
    <?php if (!empty($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <?php if (isset($artists)): ?>
        <?php if (!empty($artists)): ?>
            <ul>
                <?php foreach ($artists as $artist): ?>
                    <li>🦙 <?php echo htmlspecialchars($artist['username']) . " - " . htmlspecialchars($artist['track_count']) . " tracks"; ?>
                    </li>
                <?php endforeach; ?><?php else: ?>
            <p>No artists found.</p>
        <?php endif; ?>
    <?php endif; ?>
    <a href="main.php">메인 화면으로 돌아가기</a>
</div>

<?php include 'footer.php'; ?> 
