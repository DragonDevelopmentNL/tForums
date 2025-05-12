<?php
require_once 'includes/db.php';
session_start();
$thread_id = intval($_GET['id'] ?? 0);
$thread = $mysqli->query("SELECT t.*, f.name AS forum_name, u.username FROM threads t JOIN forums f ON t.forum_id=f.id JOIN users u ON t.user_id=u.id WHERE t.id=$thread_id")->fetch_assoc();
if (!$thread) die('Discussie niet gevonden.');
$posts = $mysqli->query("SELECT p.*, u.username, u.id as user_id, u.bio, u.avatar FROM posts p JOIN users u ON p.user_id=u.id WHERE thread_id=$thread_id ORDER BY created_at ASC");
// Reactie plaatsen
$error = '';
if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] === 'POST' && !$thread['closed']) {
    $content = trim($_POST['content'] ?? '');
    if ($content) {
        $stmt = $mysqli->prepare('INSERT INTO posts (thread_id, user_id, content, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
        $stmt->bind_param('iis', $thread_id, $_SESSION['user_id'], $content);
        $stmt->execute();
        header('Location: thread.php?id=' . $thread_id); exit;
    } else {
        $error = 'Bericht mag niet leeg zijn.';
    }
}
function isModOrAdmin() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin','moderator']);
}
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($thread['title']); ?> - Discussie</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .post { background: #f9f9fb; border-radius: 6px; margin-bottom: 18px; padding: 18px; box-shadow: 0 1px 3px #0001; display: flex; gap: 18px; align-items: flex-start; }
        .post .meta { color: #888; font-size: 0.95em; margin-bottom: 6px; }
        .post .content { font-size: 1.08em; }
        .post .actions { margin-top: 8px; }
        .post .actions a { color: #e76f51; margin-right: 10px; font-size: 0.97em; }
        .admin-actions { margin-bottom: 18px; }
        .admin-actions form { display:inline; }
        .post-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; margin-right: 8px; }
        .post-main { flex: 1; }
    </style>
</head>
<body>
<div class="header">
    <h1><?php echo htmlspecialchars($thread['title']); ?></h1>
    <p>Forum: <?php echo htmlspecialchars($thread['forum_name']); ?> | Door <?php echo htmlspecialchars($thread['username']); ?> op <?php echo date('d-m-Y H:i', strtotime($thread['created_at'])); ?>
    <?php if ($thread['closed']) echo '<span style="color:#e76f51;"> (Gesloten)</span>'; ?>
    <?php if ($thread['announcement']) echo '<span style="color:#2a9d8f;"> (Mededeling)</span>'; ?>
    </p>
    <?php if (isAdmin()): ?>
    <div class="admin-actions">
        <form method="post" action="thread_toggle_closed.php" style="display:inline;">
            <input type="hidden" name="thread_id" value="<?php echo $thread_id; ?>">
            <button class="btn" type="submit" name="action" value="<?php echo $thread['closed'] ? 'open' : 'close'; ?>">
                <?php echo $thread['closed'] ? 'Heropen discussie' : 'Sluit discussie'; ?>
            </button>
        </form>
        <form method="post" action="thread_toggle_announcement.php" style="display:inline;">
            <input type="hidden" name="thread_id" value="<?php echo $thread_id; ?>">
            <button class="btn" type="submit" name="action" value="<?php echo $thread['announcement'] ? 'remove' : 'make'; ?>">
                <?php echo $thread['announcement'] ? 'Verwijder mededeling' : 'Maak mededeling'; ?>
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>
<div class="container">
    <h2>Berichten</h2>
    <?php while($p = $posts->fetch_assoc()): ?>
        <?php
        $avatar_url = $p['avatar'] ? 'uploads/' . htmlspecialchars($p['avatar']) : 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($p['email'] ?? ''))) . '?d=mp';
        ?>
        <div class="post">
            <img class="post-avatar" src="<?php echo $avatar_url; ?>" alt="avatar">
            <div class="post-main">
                <div class="meta">Door <?php echo htmlspecialchars($p['username']); ?> op <?php echo date('d-m-Y H:i', strtotime($p['created_at'])); ?></div>
                <div class="content"><?php echo nl2br(htmlspecialchars($p['content'])); ?></div>
                <?php if ($p['bio']): ?>
                    <div class="meta" style="color:#2a9d8f; font-size:0.97em; margin-top:8px;"><em><?php echo nl2br(htmlspecialchars($p['bio'])); ?></em></div>
                <?php endif; ?>
                <?php if ((isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $p['user_id'] || isModOrAdmin()))): ?>
                <div class="actions">
                    <a href="post_edit.php?id=<?php echo $p['id']; ?>&thread_id=<?php echo $thread_id; ?>">Bewerk</a>
                    <a href="post_delete.php?id=<?php echo $p['id']; ?>&thread_id=<?php echo $thread_id; ?>" onclick="return confirm('Weet je zeker dat je dit bericht wilt verwijderen?');">Verwijder</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
    <?php if ($thread['closed']): ?>
        <div class="error">Deze discussie is gesloten.</div>
    <?php endif; ?>
    <?php if (isset($_SESSION['user_id']) && !$thread['closed']): ?>
        <h3>Reageer</h3>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <form method="post">
            <textarea name="content" rows="5" required></textarea>
            <button class="btn" type="submit">Plaatsen</button>
        </form>
    <?php elseif (!isset($_SESSION['user_id'])): ?>
        <div class="error">Log in om te reageren.</div>
    <?php endif; ?>
    <p><a href="forum.php?id=<?php echo $thread['forum_id']; ?>">&larr; Terug naar forum</a></p>
</div>
</body>
</html> 