<?php
session_start();
include 'db.php';
include 'header.php'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error_message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['portal_id']) && isset($_POST['password'])) {
        $portal_id = $_POST['portal_id'];
        $password = $_POST['password'];

        $sql = "SELECT user_id, password, user_type FROM Users WHERE portal_id = :portal_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':portal_id', $portal_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_type'] = $user['user_type'];
            header("Location: main.php"); 
            exit();
        } else {
            $error_message = "Invalid ID or Password.";
        }
    } else {
        $error_message = "Please fill in both fields.";
    }
}
?>
<div class="container">
    <h1>Login</h1>
    <?php if (!empty($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php">
        ID: <input type="text" name="portal_id" required><br>
        Password: <input type="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
</div>
<?php include 'footer.php'; ?> 
