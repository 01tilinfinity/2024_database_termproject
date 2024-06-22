<?php
session_start();
include 'db.php';
include 'header.php'; 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'general') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['playlist_id'])) {
    header("Location: main.php");
    exit();
}

$playlist_id = $_GET['playlist_id'];

$sql = "SELECT Playlists.name, Tracks.title, Artists.name AS artist_name, Composers.name AS composer_name
        FROM Playlists
        LEFT JOIN Playlist_Items ON Playlists.playlist_id = Playlist_Items.playlist_id
        LEFT JOIN Tracks ON Playlist_Items.track_id = Tracks.track_id
        LEFT JOIN Artists ON Tracks.artist_id = Artists.artist_id
        LEFT JOIN Composers ON Tracks.composer_id = Composers.composer_id
        WHERE Playlists.playlist_id = :playlist_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':playlist_id', $playlist_id);
$stmt->execute();
$playlist = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Playlist Details</h1>
    <?php if (!empty($playlist)): ?>
        <h2><?php echo htmlspecialchars($playlist[0]['name']); ?></h2>
        <ul>
            <?php foreach ($playlist as $track): ?>
                <li><?php echo htmlspecialchars($track['title']) . " - " . htmlspecialchars($track['artist_name']) . " / " . htmlspecialchars($track['composer_name']); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>플레이리스트에 곡이 없습니다.</p>
    <?php endif; ?>
    <a href="main.php">메인 화면으로 돌아가기</a>
</div>

<?php include 'footer.php'; ?> 
