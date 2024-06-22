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

if ($user_type == 'artist') {
    $sql = "SELECT * FROM Tracks WHERE artist_id = (SELECT artist_id FROM Artists WHERE user_id = :user_id)";
} elseif ($user_type == 'composer') {
    $sql = "SELECT * FROM Tracks WHERE composer_id = (SELECT composer_id FROM Composers WHERE user_id = :user_id)";
}

$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>My Tracks</h1>
    <?php if (empty($tracks)): ?>
        <p>아직 등록된 곡이 없습니다. 24시간 내에 곡 등록을 하지 않으면 계정이 삭제됩니다.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($tracks as $track): ?>
                <li>
                    <?php echo htmlspecialchars($track['title']); ?> - 
                    <?php echo floor($track['duration'] / 60) . '분 ' . ($track['duration'] % 60) . '초'; ?> -
                    <?php echo htmlspecialchars($track['release_year']); ?>
                    <a href="edit_track.php?track_id=<?php echo $track['track_id']; ?>">수정</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <a href="main.php" class="btn btn-primary">메인 화면으로 돌아가기</a> 
</div>

<?php include 'footer.php'; ?> 
