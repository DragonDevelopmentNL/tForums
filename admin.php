<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die('Geen toegang.');
require 'includes/db.php';
// Taal wijzigen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['language'])) {
    $lang = $_POST['language'] === 'en' ? 'en' : 'nl';
    $stmt = $mysqli->prepare('UPDATE settings SET forum_name=forum_name, language=? WHERE id=1');
    $stmt->bind_param('s', $lang);
    $stmt->execute();
    header('Location: admin.php'); exit;
}
$res = $mysqli->query("SELECT language FROM settings WHERE id=1");
$settings = $res->fetch_assoc();
$lang = $settings['language'] ?? 'nl';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Adminpanel</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-menu { max-width: 400px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; padding: 32px; }
        .admin-menu h2 { color: #e76f51; }
        .admin-menu ul { list-style: none; padding: 0; }
        .admin-menu li { margin: 18px 0; }
        .admin-menu a { color: #e76f51; font-size: 1.1em; text-decoration: none; }
        .admin-menu a:hover { text-decoration: underline; }
        .langform { margin-top: 24px; }
    </style>
</head>
<body>
<div class="admin-menu">
    <h2>Adminpanel</h2>
    <ul>
        <li><a href="admin_forums.php">Forums beheren</a></li>
        <li><a href="admin_users.php">Leden beheren</a></li>
        <li><a href="index.php">&larr; Terug naar forum</a></li>
    </ul>
    <form class="langform" method="post">
        <label for="language">Taal van het forum:</label>
        <select name="language" id="language">
            <option value="nl" <?php if($lang==='nl') echo 'selected'; ?>>Nederlands</option>
            <option value="en" <?php if($lang==='en') echo 'selected'; ?>>English</option>
        </select>
        <button class="btn" type="submit">Opslaan</button>
    </form>
</div>
</body>
</html> 