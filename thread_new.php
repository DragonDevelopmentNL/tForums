<?php
session_start();
if (!isset($_SESSION['user_id'])) header('Location: login.php');
require 'includes/db.php';
$forum_id = intval($_GET['forum_id'] ?? 0);
$forum = $mysqli->query("SELECT * FROM forums WHERE id=$forum_id")->fetch_assoc();
if (!$forum) die('Forum niet gevonden.');

// Check post permissions
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

if (!$can_post) {
    die('Je hebt geen rechten om in dit forum te posten.');
}

// Check age restriction
if ($forum['age_restriction'] !== 'none') {
    if (!isset($_SESSION['user_id'])) {
        die('Je moet ingelogd zijn om in dit forum te posten.');
    }
    // TODO: Add age verification system
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if (!$title || !$content) {
        $error = 'Vul alle velden in.';
    } else {
        $mysqli->begin_transaction();
        try {
            $stmt = $mysqli->prepare('INSERT INTO threads (forum_id, user_id, title, created_at) VALUES (?, ?, ?, NOW())');
            $stmt->bind_param('iis', $forum_id, $_SESSION['user_id'], $title);
            if ($stmt->execute()) {
                $thread_id = $mysqli->insert_id;
                $stmt2 = $mysqli->prepare('INSERT INTO posts (thread_id, user_id, content, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
                $stmt2->bind_param('iis', $thread_id, $_SESSION['user_id'], $content);
                if ($stmt2->execute()) {
                    $mysqli->commit();
                    header('Location: thread.php?id=' . $thread_id); 
                    exit;
                } else {
                    throw new Exception('Kon eerste bericht niet toevoegen.');
                }
            } else {
                throw new Exception('Kon discussie niet aanmaken.');
            }
        } catch (Exception $e) {
            $mysqli->rollback();
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Nieuwe discussie - <?php echo htmlspecialchars($forum['name']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="header">
    <h1>Nieuwe discussie in <?php echo htmlspecialchars($forum['name']); ?></h1>
</div>
<div class="container" style="max-width:600px;">
    <h2>Start een nieuwe discussie</h2>
    <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
    <form method="post">
        <label>Titel</label>
        <input type="text" name="title" required>
        <label>Bericht</label>
        <textarea name="content" rows="7" required></textarea>
        <button class="btn" type="submit">Plaatsen</button>
    </form>
    <p><a href="forum.php?id=<?php echo $forum_id; ?>">&larr; Terug naar forum</a></p>
</div>
</body>
</html> 