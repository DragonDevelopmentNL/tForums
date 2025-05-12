<?php
require_once 'includes/db.php';
session_start();
$forum_id = intval($_GET['id'] ?? 0);
$forum = $mysqli->query("SELECT * FROM forums WHERE id=$forum_id")->fetch_assoc();
if (!$forum) die('Forum niet gevonden.');
$threads = $mysqli->query("SELECT t.*, u.username FROM threads t JOIN users u ON t.user_id=u.id WHERE forum_id=$forum_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($forum['name']); ?> - Discussies</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="header">
    <h1><?php echo htmlspecialchars($forum['name']); ?></h1>
    <p><?php echo htmlspecialchars($forum['description']); ?></p>
</div>
<div class="container">
    <h2>Discussies</h2>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a class="btn" href="thread_new.php?forum_id=<?php echo $forum_id; ?>">+ Nieuwe discussie</a>
    <?php endif; ?>
    <ul class="forum-list">
        <?php while($t = $threads->fetch_assoc()): ?>
            <li>
                <a class="forum-title" href="thread.php?id=<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['title']); ?></a>
                <div class="forum-desc">Door <?php echo htmlspecialchars($t['username']); ?> op <?php echo date('d-m-Y H:i', strtotime($t['created_at'])); ?><?php if ($t['closed']) echo ' <span style=\'color:#e76f51\'>(Gesloten)</span>'; ?><?php if ($t['announcement']) echo ' <span style=\'color:#2a9d8f\'>(Mededeling)</span>'; ?></div>
            </li>
        <?php endwhile; ?>
    </ul>
    <p><a href="index.php">&larr; Terug naar forums</a></p>
</div>
</body>
</html> 