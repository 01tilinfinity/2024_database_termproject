<!DOCTYPE html>
<html>
<head>
    <title>Groovy Alpaca</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+IS:wght@100..400&family=IBM+Plex+Sans+KR&display=swap" rel="stylesheet">
</head>
<body>
    <div class="header">
        <div class="logo">
            <h1 class="playwrite-is-logo">Groovy Alpaca</h1>
        </div>
        <div class="user-info">
            <?php
            if (isset($_SESSION['user_id'])) {
                
                $user_id = $_SESSION['user_id'];
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

                $user_id_info = '';
                if ($user['user_type'] == 'artist') {
                    $user_id_info = "아티스트 고유ID: " . htmlspecialchars($extra_info['artist_id']);
                } elseif ($user['user_type'] == 'composer') {
                    $user_id_info = "저작권자 고유ID: " . htmlspecialchars($extra_info['composer_id']);
                } elseif ($user['user_type'] == 'general') {
                    $user_id_info = "회원 고유ID: " . htmlspecialchars($extra_info['general_id']);
                }

                echo "<p>User Type: " . htmlspecialchars($user_type_ko) . "</p>";
                echo "<p>" . $user_id_info . "</p>";
            }
            ?>
        </div>
    </div>
