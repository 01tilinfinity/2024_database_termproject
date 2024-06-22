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

$playlist_id = $_POST['playlist_id'] ?? null;

if (!$playlist_id) {
    header("Location: main.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_tracks'])) {
    $track_ids = $_POST['track_ids'] ?? [];

    if (empty($track_ids)) {
        echo "<script>alert('추가할 곡을 선택하세요.'); window.history.back();</script>";
        exit();
    }

    try {
        $conn->beginTransaction();

        $sql = "INSERT INTO Playlist_Items (playlist_id, track_id) VALUES (:playlist_id, :track_id)";
        $stmt = $conn->prepare($sql);
        foreach ($track_ids as $track_id) {
            $stmt->bindParam(':playlist_id', $playlist_id);
            $stmt->bindParam(':track_id', $track_id);
            $stmt->execute();
        }

        $conn->commit();
        echo "<script>alert('곡이 플레이리스트에 추가되었습니다.'); window.location.href='manage_playlist.php';</script>";
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

$sql = "SELECT Tracks.track_id, Tracks.title, Artists.name AS artist_name, Composers.name AS composer_name
        FROM Tracks
        LEFT JOIN Artists ON Tracks.artist_id = Artists.artist_id
        LEFT JOIN Composers ON Tracks.composer_id = Composers.composer_id
        WHERE Tracks.track_id NOT IN (
            SELECT track_id FROM Playlist_Items WHERE playlist_id = :playlist_id
        )";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':playlist_id', $playlist_id);
$stmt->execute();
$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Add to Playlist</h1>
    <?php if (empty($tracks)): ?>
        <p>추가할 수 있는 곡이 없습니다.</p>
    <?php else: ?>
        <form method="POST" action="add_to_playlist.php">
            <input type="hidden" name="playlist_id" value="<?php echo htmlspecialchars($playlist_id); ?>">
            <ul>
                <?php foreach ($tracks as $track): ?>
                    <li>
                        <input type="checkbox" name="track_ids[]" value="<?php echo htmlspecialchars($track['track_id']); ?>">
                        <?php echo htmlspecialchars($track['title']) . " - " . htmlspecialchars($track['artist_name']) . " / " . htmlspecialchars($track['composer_name']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <input type="submit" name="add_tracks" value="추가">
        </form>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
