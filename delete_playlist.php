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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $playlist_id = $_POST['playlist_id'];

    try {
        $conn->beginTransaction();
        
        $sql = "DELETE FROM Playlist_Items WHERE playlist_id = :playlist_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':playlist_id', $playlist_id);
        $stmt->execute();
        
        $sql = "DELETE FROM Playlists WHERE playlist_id = :playlist_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':playlist_id', $playlist_id);
        $stmt->execute();
        
        $conn->commit();
        echo "<script>alert('플레이리스트가 삭제되었습니다.'); window.location.href='main.php';</script>";
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<div class="container">
    <h1>Delete Playlist</h1>
    <form method="POST" action="delete_playlist.php">
        <p>정말로 플레이리스트를 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.</p>
        <input type="hidden" name="playlist_id" value="<?php echo htmlspecialchars($_POST['playlist_id']); ?>">
        <input type="submit" value="플레이리스트 삭제">
    </form>
    <a href="main.php">취소</a>
</div>

<?php include 'footer.php'; ?> 
