<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die('Geen toegang.');
require 'includes/db.php';
$id = intval($_GET['id'] ?? 0);
$forum = $mysqli->query("SELECT * FROM forums WHERE id=$id")->fetch_assoc();
if (!$forum) die('Forum niet gevonden.');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $post_perm = $_POST['post_permission'] ?? 'all';
    $read_perm = $_POST['read_permission'] ?? 'all';
    $age_rest = $_POST['age_restriction'] ?? 'none';
    
    if ($name) {
        $stmt = $mysqli->prepare('UPDATE forums SET name=?, description=?, post_permission=?, read_permission=?, age_restriction=? WHERE id=?');
        $stmt->bind_param('sssssi', $name, $desc, $post_perm, $read_perm, $age_rest, $id);
        $stmt->execute();
        header('Location: admin_forums.php'); exit;
    } else {
        $error = 'Naam is verplicht.';
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Forum bewerken</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container" style="max-width:600px;">
    <h2>Forum bewerken</h2>
    <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
    <form method="post">
        <label>Naam</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($forum['name']); ?>" required>
        <label>Omschrijving</label>
        <textarea name="description"><?php echo htmlspecialchars($forum['description']); ?></textarea>
        <label>Wie mag posten</label>
        <select name="post_permission">
            <option value="all" <?php if($forum['post_permission']==='all') echo 'selected'; ?>>Alle leden</option>
            <option value="moderators" <?php if($forum['post_permission']==='moderators') echo 'selected'; ?>>Moderators</option>
            <option value="admins" <?php if($forum['post_permission']==='admins') echo 'selected'; ?>>Admins</option>
        </select>
        <label>Wie mag lezen</label>
        <select name="read_permission">
            <option value="all" <?php if($forum['read_permission']==='all') echo 'selected'; ?>>Alle leden</option>
            <option value="moderators" <?php if($forum['read_permission']==='moderators') echo 'selected'; ?>>Moderators</option>
            <option value="admins" <?php if($forum['read_permission']==='admins') echo 'selected'; ?>>Admins</option>
        </select>
        <label>Leeftijdsgrens</label>
        <select name="age_restriction">
            <option value="none" <?php if($forum['age_restriction']==='none') echo 'selected'; ?>>Geen</option>
            <option value="12+" <?php if($forum['age_restriction']==='12+') echo 'selected'; ?>>12+</option>
            <option value="16+" <?php if($forum['age_restriction']==='16+') echo 'selected'; ?>>16+</option>
            <option value="18+" <?php if($forum['age_restriction']==='18+') echo 'selected'; ?>>18+</option>
        </select>
        <button class="btn" type="submit">Opslaan</button>
    </form>
    <p><a href="admin_forums.php">&larr; Terug naar forums</a></p>
</div>
</body>
</html> 