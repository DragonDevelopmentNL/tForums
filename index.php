<?php
require_once 'includes/db.php';
require_once 'includes/lang.php';
$res = $mysqli->query("SELECT forum_name, forum_description, language FROM settings WHERE id=1");
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
</head>
<body>
<div class="header">
    <h1><?php echo htmlspecialchars($settings['forum_name']); ?></h1>
    <p><?php echo htmlspecialchars($settings['forum_description']); ?></p>
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
</body>
</html> 