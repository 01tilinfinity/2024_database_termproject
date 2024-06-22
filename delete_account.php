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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $user_type = $_SESSION['user_type'];
    
    try {
        $conn->beginTransaction();

        if ($user_type == 'general') {
        
            $sql = "DELETE FROM Playlist_Items WHERE playlist_id IN (SELECT playlist_id FROM Playlists WHERE user_id = :user_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $sql = "DELETE FROM Playlists WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

         
            $sql = "DELETE FROM GeneralUsers WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        } elseif ($user_type == 'artist') {
            
            $sql = "DELETE FROM Tracks WHERE artist_id = (SELECT artist_id FROM Artists WHERE user_id = :user_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
          
            $sql = "DELETE FROM Artists WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        } elseif ($user_type == 'composer') {
         
            $sql = "DELETE FROM Tracks WHERE composer_id = (SELECT composer_id FROM Composers WHERE user_id = :user_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
          
            $sql = "DELETE FROM Composers WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        }

     
        $sql = "DELETE FROM Users WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $conn->commit();
        
     
        session_destroy();

        echo "<script>alert('회원 정보가 삭제되었습니다.'); window.location.href='login.php';</script>";
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
<div class="container">
    <h1>Delete Account</h1>
    <form method="POST" action="delete_account.php">
        <p>정말로 회원 정보를 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.</p>
        <input type="submit" value="회원 정보 삭제">
    </form>
    <a href="main.php">취소</a>
</div>
<?php include 'footer.php'; ?> 
