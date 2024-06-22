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
$sql = "SELECT COUNT(*) AS playlist_count FROM Playlists WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['playlist_count'] > 0) {
    echo "<script>alert('플레이리스트를 이미 생성하셨습니다.'); window.location.href='main.php';</script>";
    exit();
}

$sql = "SELECT Tracks.track_id, Tracks.title, Artists.name AS artist_name, Composers.name AS composer_name
        FROM Tracks
        LEFT JOIN Artists ON Tracks.artist_id = Artists.artist_id
        LEFT JOIN Composers ON Tracks.composer_id = Composers.composer_id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $playlist_name = $_POST['playlist_name'];
    $track_ids = $_POST['track_ids'];

    if (empty($track_ids)) {
        echo "<script>alert('플레이리스트에는 최소 1곡 이상의 노래가 포함되어야 합니다.'); window.history.back();</script>";
        exit();
    }

    try {
        $conn->beginTransaction();

        $sql = "INSERT INTO Playlists (user_id, name) VALUES (:user_id, :name)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':name', $playlist_name);
        $stmt->execute();
        $playlist_id = $conn->lastInsertId();

        $sql = "INSERT INTO Playlist_Items (playlist_id, track_id) VALUES (:playlist_id, :track_id)";
        $stmt = $conn->prepare($sql);
        foreach ($track_ids as $track_id) {
            $stmt->bindParam(':playlist_id', $playlist_id);
            $stmt->bindParam(':track_id', $track_id);
            $stmt->execute();
        }

        $conn->commit();
        echo "<script>alert('플레이리스트가 성공적으로 생성되었습니다.'); window.location.href='main.php';</script>";
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
<div class="container">
    <h1>Create Playlist</h1>
    <form method="POST" action="create_playlist.php">
        플레이리스트 이름: <input type="text" name="playlist_name" required><br>
        <h3>노래 선택:</h3>
        <?php foreach ($tracks as $track): ?>
            <input type="checkbox" name="track_ids[]" value="<?php echo $track['track_id']; ?>">
            <?php echo htmlspecialchars($track['title']) . " - " . htmlspecialchars($track['artist_name']) . " / " . htmlspecialchars($track['composer_name']); ?><br>
        <?php endforeach; ?>
        <input type="submit" value="Create Playlist">
    </form>
</div>
<?php include 'footer.php'; ?>
