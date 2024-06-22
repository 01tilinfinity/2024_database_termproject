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

$track_id = $_GET['track_id'] ?? null;

if (!$track_id) {
    header("Location: main.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT Tracks.track_id, Tracks.title, Tracks.duration, Artists.user_id AS artist_user_id, Composers.user_id AS composer_user_id
        FROM Tracks
        LEFT JOIN Artists ON Tracks.artist_id = Artists.artist_id
        LEFT JOIN Composers ON Tracks.composer_id = Composers.composer_id
        WHERE Tracks.track_id = :track_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':track_id', $track_id);
$stmt->execute();
$track = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$track || ($track['artist_user_id'] != $user_id && $track['composer_user_id'] != $user_id)) {
    echo "<script>alert('You do not have permission to edit this track.'); window.location.href='main.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $duration = $_POST['duration'];

    try {
        $sql = "UPDATE Tracks SET title = :title, duration = :duration WHERE track_id = :track_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':track_id', $track_id);
        $stmt->execute();

        echo "<script>alert('Track updated successfully.'); window.location.href='view_my_tracks.php';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<div class="container">
    <h1>Edit Track</h1>
    <form method="POST" action="edit_track.php?track_id=<?php echo htmlspecialchars($track_id); ?>">
        Title: <input type="text" name="title" value="<?php echo htmlspecialchars($track['title']); ?>" required><br>
        Duration (in seconds): <input type="number" name="duration" value="<?php echo htmlspecialchars($track['duration']); ?>" required><br>
        <input type="submit" value="Update">
    </form>
</div>
<?php include 'footer.php'; ?> 
