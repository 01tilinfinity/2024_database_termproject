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

$user_id = $_SESSION['user_id'];
try {
    $sql = "SELECT username, user_type FROM Users WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $user_type_ko = '';
    if ($user['user_type'] == 'general') {
        $user_type_ko = '일반 회원';
    } elseif ($user['user_type'] == 'artist') {
        $user_type_ko = '아티스트';
    } elseif ($user['user_type'] == 'composer') {
        $user_type_ko = '저작권자';
    }

    if ($user['user_type'] == 'artist') {
        $sql = "SELECT artist_id FROM Artists WHERE user_id = :user_id";
    } elseif ($user['user_type'] == 'composer') {
        $sql = "SELECT composer_id FROM Composers WHERE user_id = :user_id";
    } elseif ($user['user_type'] == 'general') {
        $sql = "SELECT general_id FROM GeneralUsers WHERE user_id = :user_id";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $extra_info = $stmt->fetch(PDO::FETCH_ASSOC);

    $playlist_sql = "SELECT playlist_id FROM Playlists WHERE user_id = :user_id";
    $playlist_stmt = $conn->prepare($playlist_sql);
    $playlist_stmt->bindParam(':user_id', $user_id);
    $playlist_stmt->execute();
    $playlist = $playlist_stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<div class="container">
    <h1 class="welcome">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>

    <div class="search-section">
        <h2>Search</h2>
        <form method="GET" action="search.php" class="search-form">
            <select name="type" class="search-select">
                <option value="artist">아티스트</option>
                <option value="composer">저작권자</option>
                <option value="track">곡</option>
            </select>
            <input type="text" name="query" placeholder="검색어 입력" required class="search-input">
            <input type="submit" value="검색" class="search-button">
        </form>
    </div>

    <ul class="menu">
        <?php if ($user['user_type'] == 'general'): ?>
            <li><a href="create_playlist.php" class="btn">플레이리스트 생성</a></li>
            <li><a href="view_playlist.php" class="btn">플레이리스트 보기</a></li>
            <li><a href="manage_playlist.php" class="btn">플레이리스트 관리</a></li>
            <?php if ($playlist): ?>
                <li>
                    <form method="POST" action="delete_playlist.php" class="inline-form">
                        <input type="hidden" name="playlist_id" value="<?php echo htmlspecialchars($playlist['playlist_id']); ?>">
                        <input type="submit" value="플레이리스트 삭제" class="btn">
                    </form>
                </li>
            <?php endif; ?>
            <li><a href="delete_account.php" class="btn">회원 정보 삭제</a></li>
        <?php endif; ?>
        
        <?php if ($user['user_type'] == 'artist'): ?>
            <li><a href="add_track.php" class="btn">곡 추가</a></li>
            <li><a href="view_my_tracks.php" class="btn">내 곡 보기</a></li>
            <li><a href="edit_artist.php" class="btn">아티스트 정보 수정</a></li>
            <li><a href="delete_account.php" class="btn">회원 정보 삭제</a></li>
        <?php endif; ?>
        
        <?php if ($user['user_type'] == 'composer'): ?>
            <li><a href="add_track.php" class="btn">곡 추가</a></li>
            <li><a href="view_my_tracks.php" class="btn">내 곡 보기</a></li>
            <li><a href="edit_composer.php" class="btn">저작권자 정보 수정</a></li>
            <li><a href="delete_account.php" class="btn">회원 정보 삭제</a></li>
        <?php endif; ?>
        
        <li><a href="logout.php" class="btn">로그아웃</a></li>
    </ul>
</div>

<?php include 'footer.php'; ?> 
