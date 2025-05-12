<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die('Geen toegang.');
require 'includes/db.php';
// Ban/opheffen ban
if (isset($_GET['ban'])) {
    $uid = intval($_GET['ban']);
    $mysqli->query("UPDATE users SET banned=1 WHERE id=$uid");
    header('Location: admin_users.php'); exit;
}
if (isset($_GET['unban'])) {
    $uid = intval($_GET['unban']);
    $mysqli->query("UPDATE users SET banned=0 WHERE id=$uid");
    header('Location: admin_users.php'); exit;
}
// Verwijderen
if (isset($_GET['delete'])) {
    $uid = intval($_GET['delete']);
    $mysqli->query("DELETE FROM users WHERE id=$uid");
    header('Location: admin_users.php'); exit;
}
// Rol wijzigen
if (isset($_GET['role']) && isset($_GET['uid'])) {
    $uid = intval($_GET['uid']);
    $role = in_array($_GET['role'], ['user','moderator','admin']) ? $_GET['role'] : 'user';
    $mysqli->query("UPDATE users SET role='$role' WHERE id=$uid");
    header('Location: admin_users.php'); exit;
}
$users = $mysqli->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Leden beheren</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .user-admin-list td { padding: 8px 12px; }
        .user-admin-list th { color: #e76f51; }
        .banned { color: #c00; }
        .roleform { display:inline; margin-left:8px; }
    </style>
</head>
<body>
<div class="container" style="max-width:800px;">
    <h2>Leden beheren</h2>
    <table class="user-admin-list">
        <tr><th>ID</th><th>Gebruikersnaam</th><th>E-mail</th><th>Rol</th><th>Status</th><th>Acties</th></tr>
        <?php while($u = $users->fetch_assoc()): ?>
        <tr>
            <td><?php echo $u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['username']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td>
                <?php echo $u['role']; ?>
                <form class="roleform" method="get" action="admin_users.php">
                    <input type="hidden" name="uid" value="<?php echo $u['id']; ?>">
                    <select name="role" onchange="this.form.submit()">
                        <option value="user" <?php if($u['role']==='user') echo 'selected'; ?>>user</option>
                        <option value="moderator" <?php if($u['role']==='moderator') echo 'selected'; ?>>moderator</option>
                        <option value="admin" <?php if($u['role']==='admin') echo 'selected'; ?>>admin</option>
                    </select>
                </form>
            </td>
            <td><?php echo $u['banned'] ? '<span class="banned">Geblokkeerd</span>' : 'Actief'; ?></td>
            <td>
                <?php if ($u['banned']): ?>
                    <a href="admin_users.php?unban=<?php echo $u['id']; ?>">Opheffen ban</a>
                <?php else: ?>
                    <a href="admin_users.php?ban=<?php echo $u['id']; ?>">Ban</a>
                <?php endif; ?> |
                <a href="admin_users.php?delete=<?php echo $u['id']; ?>" onclick="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?');">Verwijder</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <p><a href="admin.php">&larr; Terug naar adminpanel</a></p>
</div>
</body>
</html> 