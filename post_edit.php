<?php
require_once 'includes/db.php';
session_start();
$id = intval($_GET['id'] ?? 0);
$thread_id = intval($_GET['thread_id'] ?? 0);
$post = $mysqli->query("SELECT * FROM posts WHERE id=$id")->fetch_assoc();
if (!$post) die('Post niet gevonden.');
$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? '';
if (!($user_id == $post['user_id'] || in_array($role, ['admin','moderator']))) die('Geen toegang.');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');
    if ($content) {
        $stmt = $mysqli->prepare('UPDATE posts SET content=?, updated_at=NOW() WHERE id=?');
        $stmt->bind_param('si', $content, $id);
        $stmt->execute();
        header('Location: thread.php?id=' . $thread_id); exit;
    } else {
        $error = 'Bericht mag niet leeg zijn.';
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bericht bewerken</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container" style="max-width:600px;">
    <h2>Bericht bewerken</h2>
    <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
    <form method="post">
        <textarea name="content" rows="7" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        <button class="btn" type="submit">Opslaan</button>
    </form>
    <p><a href="thread.php?id=<?php echo $thread_id; ?>">&larr; Terug naar discussie</a></p>
</div>
</body>
</html> 