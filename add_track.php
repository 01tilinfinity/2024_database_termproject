<?php
session_start();
include 'db.php';
include 'header.php'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] != 'artist' && $_SESSION['user_type'] != 'composer')) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $duration_minutes = $_POST['duration_minutes'];
    $duration_seconds = $_POST['duration_seconds'];
    $release_year = $_POST['release_year'];

    $duration = ($duration_minutes * 60) + $duration_seconds;

    if ($user_type == 'artist') {
        $sql = "SELECT artist_id FROM Artists WHERE user_id = :user_id";
    } elseif ($user_type == 'composer') {
        $sql = "SELECT composer_id FROM Composers WHERE user_id = :user_id";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $owner_id = $result ? ($user_type == 'artist' ? $result['artist_id'] : $result['composer_id']) : null;

    try {
        if ($user_type == 'artist') {
            $sql = "INSERT INTO Tracks (title, duration, release_year, artist_id) VALUES (:title, :duration, :release_year, :owner_id)";
        } elseif ($user_type == 'composer') {
            $sql = "INSERT INTO Tracks (title, duration, release_year, composer_id) VALUES (:title, :duration, :release_year, :owner_id)";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':release_year', $release_year);
        $stmt->bindParam(':owner_id', $owner_id);
        $stmt->execute();
        header("Location: main.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<div class="container">
    <h1>Add Track</h1>
    <form method="POST" action="add_track.php" class="track-form">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required>
        </div>
        <div class="form-group">
            <label for="duration_minutes">Duration:</label>
            <input type="number" name="duration_minutes" id="duration_minutes" placeholder="Minutes" required>
            <input type="number" name="duration_seconds" id="duration_seconds" placeholder="Seconds" required>
        </div>
        <div class="form-group">
            <label for="release_year">Release Year:</label>
            <input type="number" name="release_year" id="release_year" required>
        </div>
        <input type="submit" value="Add Track" class="submit-button">
    </form>
</div>

<?php include 'footer.php'; ?> 