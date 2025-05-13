<?php
require_once 'includes/db.php';
session_start();
$forum_id = intval($_GET['id'] ?? 0);
$forum = $mysqli->query("SELECT * FROM forums WHERE id=$forum_id")->fetch_assoc();
if (!$forum) die('Forum niet gevonden.');

// Check read permissions
$can_read = false;
if (isset($_SESSION['role'])) {
    if ($forum['read_permission'] === 'all') {
        $can_read = true;
    } elseif ($forum['read_permission'] === 'moderators' && in_array($_SESSION['role'], ['moderator', 'admin'])) {
        $can_read = true;
    } elseif ($forum['read_permission'] === 'admins' && $_SESSION['role'] === 'admin') {
        $can_read = true;
    }
} else {
    $can_read = ($forum['read_permission'] === 'all');
}

if (!$can_read) {
    die('Je hebt geen toegang tot dit forum.');
}

// Check age restriction
if ($forum['age_restriction'] !== 'none') {
    if (!isset($_SESSION['user_id'])) {
        die('Je moet ingelogd zijn om dit forum te bekijken.');
    }
    // TODO: Add age verification system
}

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
    <?php if ($forum['age_restriction'] !== 'none'): ?>
        <p class="age-restriction">Leeftijdsgrens: <?php echo htmlspecialchars($forum['age_restriction']); ?></p>
    <?php endif; ?>
</div>
<div class="container">
    <h2>Discussies</h2>
    <?php 
    $can_post = false;
    if (isset($_SESSION['role'])) {
        if ($forum['post_permission'] === 'all') {
            $can_post = true;
        } elseif ($forum['post_permission'] === 'moderators' && in_array($_SESSION['role'], ['moderator', 'admin'])) {
            $can_post = true;
        } elseif ($forum['post_permission'] === 'admins' && $_SESSION['role'] === 'admin') {
            $can_post = true;
        }
    }
    
    if ($can_post): 
    ?>
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