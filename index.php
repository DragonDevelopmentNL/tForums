<?php
require_once 'includes/db.php';
require_once 'includes/lang.php';
session_start();
$res = $mysqli->query("SELECT forum_name, forum_description, language, logo_url, footer_text FROM settings WHERE id=1");
$settings = $res->fetch_assoc();
$lang = $settings['language'] ?? 'nl';
$t = $langs[$lang];
$forums = $mysqli->query("SELECT * FROM forums");
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($settings['forum_name']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .topbar { background: #fff; border-bottom: 1px solid #ececec; padding: 10px 0; display: flex; justify-content: flex-end; align-items: center; }
        .topbar .nav { display: flex; gap: 18px; margin-right: 32px; }
        .topbar .nav a { color: #e76f51; text-decoration: none; font-weight: 500; }
        .topbar .nav a:hover { text-decoration: underline; }
        .forum-logo { max-height: 60px; margin: 0 auto; display: block; }
        .header { text-align: center; margin: 20px 0; }
        .header h1 { margin: 0; color: #e76f51; }
        .header p { margin: 10px 0 0; color: #666; }
        .footer { margin-top: 40px; padding: 20px 0; border-top: 1px solid #ececec; color: #666; text-align: center; }
    </style>
</head>
<body>
<div class="topbar">
    <div class="nav">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login.php"><?php echo $t['login']; ?></a>
            <a href="register.php"><?php echo $t['register']; ?></a>
        <?php else: ?>
            <a href="profile.php">Mijn Profiel</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin.php">Admin</a>
            <?php endif; ?>
            <a href="logout.php"><?php echo $t['logout']; ?></a>
        <?php endif; ?>
    </div>
</div>
<div class="header">
    <?php if (!empty($settings['logo_url'])): ?>
        <img src="<?php echo htmlspecialchars($settings['logo_url']); ?>" alt="Forum logo" class="forum-logo">
    <?php else: ?>
        <h1><?php echo htmlspecialchars($settings['forum_name']); ?></h1>
        <p><?php echo htmlspecialchars($settings['forum_description']); ?></p>
    <?php endif; ?>
</div>
<div class="container">
    <h2><?php echo $t['forums']; ?></h2>
    <ul class="forum-list">
        <?php while($f = $forums->fetch_assoc()): ?>
            <li>
                <a class="forum-title" href="forum.php?id=<?php echo $f['id']; ?>"><?php echo htmlspecialchars($f['name']); ?></a>
                <div class="forum-desc"><?php echo htmlspecialchars($f['description']); ?></div>
            </li>
        <?php endwhile; ?>
    </ul>
</div>
<?php if (!empty($settings['footer_text'])): ?>
<div class="footer">
    <?php echo nl2br(htmlspecialchars($settings['footer_text'])); ?>
</div>
<?php endif; ?>
</body>
</html> 