<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die('Geen toegang.');
require 'includes/db.php';
// Forum toevoegen
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_forum'])) {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if ($name) {
        $stmt = $mysqli->prepare('INSERT INTO forums (name, description) VALUES (?, ?)');
        $stmt->bind_param('ss', $name, $desc);
        $stmt->execute();
        header('Location: admin_forums.php'); exit;
    } else {
        $error = 'Naam is verplicht.';
    }
}
// Forum verwijderen
if (isset($_GET['delete'])) {
    $fid = intval($_GET['delete']);
    $mysqli->query("DELETE FROM forums WHERE id=$fid");
    header('Location: admin_forums.php'); exit;
}
$forums = $mysqli->query('SELECT * FROM forums');
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Forums beheren</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .forum-admin-list { margin-bottom: 32px; }
        .forum-admin-list td { padding: 8px 12px; }
        .forum-admin-list th { color: #e76f51; }
    </style>
</head>
<body>
<div class="container" style="max-width:700px;">
    <h2>Forums beheren</h2>
    <table class="forum-admin-list">
        <tr><th>ID</th><th>Naam</th><th>Omschrijving</th><th>Acties</th></tr>
        <?php while($f = $forums->fetch_assoc()): ?>
        <tr>
            <td><?php echo $f['id']; ?></td>
            <td><?php echo htmlspecialchars($f['name']); ?></td>
            <td><?php echo htmlspecialchars($f['description']); ?></td>
            <td>
                <a href="admin_forum_edit.php?id=<?php echo $f['id']; ?>">Bewerk</a> |
                <a href="admin_forums.php?delete=<?php echo $f['id']; ?>" onclick="return confirm('Weet je zeker dat je dit forum wilt verwijderen?');">Verwijder</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <h3>Nieuw forum toevoegen</h3>
    <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
    <form method="post">
        <label>Naam</label>
        <input type="text" name="name" required>
        <label>Omschrijving</label>
        <textarea name="description"></textarea>
        <button class="btn" type="submit" name="add_forum">Toevoegen</button>
    </form>
    <p><a href="admin.php">&larr; Terug naar adminpanel</a></p>
</div>
</body>
</html> 