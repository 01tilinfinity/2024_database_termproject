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

$error_message = ""; 
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['query']) && isset($_GET['type'])) {
    $query = $_GET['query'];
    $type = $_GET['type'];

    try {
        if ($type == 'artist') {
            header("Location: search_artists.php?query=" . urlencode($query));
            exit();
        } elseif ($type == 'composer') {
            header("Location: search_composers.php?query=" . urlencode($query));
            exit();
        } elseif ($type == 'track') {
            header("Location: search_tracks.php?query=" . urlencode($query));
            exit();
        }
    } catch (Exception $e) {
        $error_message = '검색 중 오류가 발생했습니다: ' . $e->getMessage();
    }
}
?>

<div class="container">
    <h1>Search</h1>
    <form method="GET" action="search.php">
        <select name="type">
            <option value="artist">아티스트</option>
            <option value="composer">저작권자</option>
            <option value="track">곡</option>
        </select>
        <input type="text" name="query" placeholder="검색어 입력" required>
        <input type="submit" value="검색">
    </form>
    <?php if (!empty($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <a href="main.php">메인 화면으로 돌아가기</a>
</div>

<style>
    .search-form {
urn-button"tent: space-lign-items: center;
        margin-bottom: 20px;
    }
    .search-type {
        flex: 1;
        text-align: left?php include 'footer.php'; ?> 

<style>
    .return-button {
padding: 10px 20px;
      backgr

<style>
    .return-button {
     display: inline-block;
        padding: 10px 20px;
        background-color: #FFE9D0;
        color: #A0937D;
        text-decoration: none;
        border: 2px solid #A0937D;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .return-button:hover {
        background-color: #A0937D;
        color: #FFE9D0;
    }
</style#FFE9D0;
    }   .-align: right;
    }
    .search-input input[type="text"] {
      border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s ease, color 0.3s ease;
      
 p   ut input[type="submit"] {
        padding: 5px 10px;
        font-size: 16poter.php'; ?> 
