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

        $sql = "SELECT Tracks.track_id, Tracks.title, Artists.name AS artist_name, Composers.name AS composer_name
                FROM Tracks
                LEFT JOIN Artists ON Tracks.artist_id = Artists.artist_id
                LEFT JOIN Composers ON Tracks.composer_id = Composers.composer_id
                WHERE Tracks.title LIKE :query";
        $stmt = $conn->prepare($sql);
        $search_query = '%' . $query . '%';
        $stmt->bindParam(':query', $search_query, PDO::PARAM_STR);
        $stmt->execute();
        $tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = '검색 중 오류가 발생했습니다: ' . $e->getMessage();
    }
}
?>

<div class="container">
    <h1>Search Tracks</h1>
    <form method="GET" action="search_tracks.php">
        <input type="text" name="query" placeholder="Search Tracks" required>
        <input type="submit" value="Search">
    </form>
    <?php if (!empty($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <?php if (isset($tracks)): ?>
        <?php if (!empty($tracks)): ?>
            <ul>
                <?php foreach ($tracks as $track): ?>
                    <li>🦙 <?php echo htmlspecialchars($track['title']) . " - " . htmlspecialchars($track['artist_name']) . " / " . htmlspecialchars($track['composer_name']); ?>
                    </li>
                <?php endforeach; ?><?php else: ?>
            <p>No tracks found.</p>
        <?php endif; ?>
    <?php endif; ?>
    <a href="main.php">메인 화면으로 돌아가기</a>
</div>

<?php include 'footer.php'; ?> 
