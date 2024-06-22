<?php
session_start();
include 'db.php';
include 'header.php'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'composer') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $user_id = $_SESSION['user_id'];

    try {
        $sql = "UPDATE Users SET username = :username, email = :email WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        echo "<script>alert('정보가 성공적으로 수정되었습니다.'); window.location.href='main.php';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT username, email FROM Users WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<div class="container">
    <h1>Edit Composer Information</h1>
    <form method="POST" action="edit_composer.php">
        Username: <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br>
        Email: <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>
        <input type="submit" value="Update">
    </form>
</div>
<?php include 'footer.php'; ?> 