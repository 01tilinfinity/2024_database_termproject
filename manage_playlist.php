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

$user_id = $_SESSION['user_id'];

$sql = "SELECT Playlists.playlist_id, Playlists.name, Tracks.track_id, Tracks.title, Artists.name AS artist_name, Composers.name AS composer_name
        FROM Playlists
        LEFT JOIN Playlist_Items ON Playlists.playlist_id = Playlist_Items.playlist_id
        LEFT JOIN Tracks ON Playlist_Items.track_id = Tracks.track_id
        LEFT JOIN Artists ON Tracks.artist_id = Artists.artist_id
        LEFT JOIN Composers ON Tracks.composer_id = Composers.composer_id
        WHERE Playlists.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_track'])) {
    $playlist_id = $_POST['playlist_id'];
    $track_id = $_POST['track_id'];

    $sql = "SELECT COUNT(*) AS track_count FROM Playlist_Items WHERE playlist_id = :playlist_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':playlist_id', $playlist_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['track_count'] <= 1) {
        echo "<script>alert('플레이리스트에 최소 1곡 이상 있어야 합니다.'); window.location.href='manage_playlist.php';</script>";
    } else {
        $sql = "DELETE FROM Playlist_Items WHERE playlist_id = :playlist_id AND track_id = :track_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':playlist_id', $playlist_id);
        $stmt->bindParam(':track_id', $track_id);
        $stmt->execute();
        
        echo "<script>alert('곡이 플레이리스트에서 삭제되었습니다.'); window.location.href='manage_playlist.php';</script>";
    }
}
?>
<div class="container">
    <h1>플레이리스트 관리</h1>
    <?php if (empty($playlists)): ?>
        <p>플레이리스트가 없습니다. 생성 후에 다시 시도하세요.</p>
    <?php else: ?>
        <h2><?php echo htmlspecialchars($playlists[0]['name']); ?></h2>
        <form method="POST" action="add_to_playlist.php">
            <input type="hidden" name="playlist_id" value="<?php echo htmlspecialchars($playlists[0]['playlist_id']); ?>">
            <input type="submit" value="곡 추가">
        </form>
        <ul>
            <?php foreach ($playlists as $playlist): ?>
                <li>
                    <?php echo htmlspecialchars($playlist['title']) . " - " . htmlspecialchars($playlist['artist_name']) . " / " . htmlspecialchars($playlist['composer_name']); ?>
                    <form method="POST" action="manage_playlist.php" style="display:inline;">
                        <input type="hidden" name="playlist_id" value="<?php echo htmlspecialchars($playlist['playlist_id']); ?>">
                        <input type="hidden" name="track_id" value="<?php echo htmlspecialchars($playlist['track_id']); ?>">
                        <input type="submit" name="delete_track" value="삭제">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?> 
