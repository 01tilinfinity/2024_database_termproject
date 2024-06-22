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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['query'])) {
    $query = $_GET['query'];

    $sql = "SELECT Playlists.playlist_id, Playlists.name, Users.username AS owner
            FROM Playlists
            JOIN Users ON Playlists.user_id = Users.user_id
            WHERE Playlists.name LIKE :query";
    $stmt = $conn->prepare($sql);
    $search_query = '%' . $query . '%';
    $stmt->bindParam(':query', $search_query);
    $stmt->execute();
    $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container">
    <h1>Search Results</h1>
    <?php if (!empty($playlists)): ?>
        <ul>
            <?php foreach ($playlists as $playlist): ?>
                <li>
                    <a href="view_playlist_details.php?playlist_id=<?php echo htmlspecialchars($playlist['playlist_id']); ?>">
                        <?php echo htmlspecialchars($playlist['name']) . " (by " . htmlspecialchars($playlist['owner']) . ")"; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>검색된 플레이리스트가 없습니다.</p>
    <?php endif; ?>
    <a href="main.php">메인 화면으로 돌아가기</a>
</div>

<?php include 'footer.php'; ?> 
