<?php
session_start();
include 'db.php';
include 'header.php'; 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function generateUniqueId($conn, $prefix) {
    $date = date("Ymd");
    $sql = "SELECT COUNT(*) AS user_count FROM Users WHERE user_id LIKE :prefix_date";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':prefix_date', $prefix . $date . '%');
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_count = $result['user_count'] + 1; 
    $unique_id = $prefix . $date . str_pad($user_count, 3, '0', STR_PAD_LEFT);
    return $unique_id;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn->beginTransaction();

        $portal_id = $_POST['portal_id'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = $_POST['email'];
        $user_type = $_POST['user_type']; // 'general', 'artist', 'composer'
        $debut_year = $_POST['debut_year'];

        if ($user_type == 'general') {
            $unique_id = generateUniqueId($conn, 'M');
            $sql = "INSERT INTO Users (user_id, portal_id, username, password, email, user_type) VALUES (:user_id, :portal_id, :username, :password, :email, :user_type)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $unique_id);
            $stmt->bindParam(':portal_id', $portal_id);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':user_type', $user_type);
            if ($stmt->execute()) {
                $sql = "INSERT INTO GeneralUsers (general_id, user_id) VALUES (:unique_id, :user_id)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':unique_id', $unique_id);
                $stmt->bindParam(':user_id', $unique_id);
                $stmt->execute();
            }
        } elseif ($user_type == 'artist') {
            $unique_id = generateUniqueId($conn, 'A');
            $sql = "INSERT INTO Users (user_id, portal_id, username, password, email, user_type) VALUES (:user_id, :portal_id, :username, :password, :email, :user_type)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $unique_id);
            $stmt->bindParam(':portal_id', $portal_id);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':user_type', $user_type);
            if ($stmt->execute()) {
                $sql = "INSERT INTO Artists (artist_id, user_id, name, debut_year, portal_id) VALUES (:unique_id, :user_id, :name, :debut_year, :portal_id)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':unique_id', $unique_id);
                $stmt->bindParam(':user_id', $unique_id);
                $stmt->bindParam(':name', $username); 
                $stmt->bindParam(':debut_year', $debut_year);
                $stmt->bindParam(':portal_id', $portal_id);
                $stmt->execute();
            }
        } elseif ($user_type == 'composer') {
            $unique_id = generateUniqueId($conn, 'C');
            $sql = "INSERT INTO Users (user_id, portal_id, username, password, email, user_type) VALUES (:user_id, :portal_id, :username, :password, :email, :user_type)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $unique_id);
            $stmt->bindParam(':portal_id', $portal_id);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':user_type', $user_type);
            if ($stmt->execute()) {
                $sql = "INSERT INTO Composers (composer_id, user_id, name, debut_year, portal_id) VALUES (:unique_id, :user_id, :name, :debut_year, :portal_id)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':unique_id', $unique_id);
                $stmt->bindParam(':user_id', $unique_id);
                $stmt->bindParam(':name', $username); 
                $stmt->bindParam(':debut_year', $debut_year);
                $stmt->bindParam(':portal_id', $portal_id);
                $stmt->execute();
            }
        }

        $conn->commit();
        header("Location: login.php"); 
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
<div class="container">
    <h1>Register</h1>
    <form method="POST" action="register.php">
        ID: <input type="text" name="portal_id" required><br>
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        Email: <input type="email" name="email" required><br>
        User Type: <select name="user_type" id="user_type" onchange="toggleDebutYear()">
            <option value="general">일반 회원</option>
            <option value="artist">아티스트 회원</option>
            <option value="composer">저작권자 회원</option>
        </select><br>
        <div id="additional_fields" style="display:none;">
            데뷔 년도: <input type="text" name="debut_year"><br>
        </div>
        <input type="submit" value="Register">
    </form>
</div>

<script>
    function toggleDebutYear() {
        var userType = document.getElementById("user_type").value;
        var additionalFields = document.getElementById("additional_fields");
        if (userType === "general") {
            additionalFields.style.display = "none";
        } else {
            additionalFields.style.display = "block";
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleDebutYear();
    });
</script>

<?php include 'footer.php'; ?> 
