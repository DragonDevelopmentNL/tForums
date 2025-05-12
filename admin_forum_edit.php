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
    if ($name) {
        $stmt = $mysqli->prepare('UPDATE forums SET name=?, description=? WHERE id=?');
        $stmt->bind_param('ssi', $name, $desc, $id);
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
        <button class="btn" type="submit">Opslaan</button>
    </form>
    <p><a href="admin_forums.php">&larr; Terug naar forums</a></p>
</div>
</body>
</html> 