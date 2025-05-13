<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die('Geen toegang.');
require 'includes/db.php';
require 'includes/lang.php';
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
$lang = $settings['language'] ?? 'en';
$t = $langs[$lang];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $t['admin_panel']; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-topbar { background: #fff; border-bottom: 1px solid #ececec; padding: 10px 0; display: flex; gap: 24px; align-items: center; margin-bottom: 24px; }
        .admin-topbar a { color: #e76f51; text-decoration: none; font-weight: 500; margin-left: 32px; }
        .admin-topbar a:hover { text-decoration: underline; }
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
<div class="admin-topbar">
    <a href="admin_forums.php"><?php echo $t['forum_admin']; ?></a>
    <a href="admin_users.php"><?php echo $t['user_admin']; ?></a>
    <a href="settings.php"><?php echo $t['settings']; ?></a>
    <a href="index.php">&larr; <?php echo $t['back']; ?></a>
</div>
<div class="admin-menu">
    <h2><?php echo $t['admin_panel']; ?></h2>
    <form class="langform" method="post">
        <label for="language"><?php echo $t['language']; ?>:</label>
        <select name="language" id="language">
            <option value="nl" <?php if($lang==='nl') echo 'selected'; ?>><?php echo $t['dutch']; ?></option>
            <option value="en" <?php if($lang==='en') echo 'selected'; ?>><?php echo $t['english']; ?></option>
        </select>
        <button class="btn" type="submit"><?php echo $t['save']; ?></button>
    </form>
</div>
</body>
</html> 