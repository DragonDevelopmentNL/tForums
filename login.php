<?php
session_start();
if (isset($_SESSION['user_id'])) header('Location: index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'includes/db.php';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $mysqli->prepare('SELECT id, password, role, banned FROM users WHERE username=?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hash, $role, $banned);
        $stmt->fetch();
        if ($banned) {
            $error = 'Je account is geblokkeerd.';
        } elseif (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            header('Location: index.php'); exit;
        } else {
            $error = 'Ongeldige inloggegevens.';
        }
    } else {
        $error = 'Ongeldige inloggegevens.';
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Inloggen</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container" style="max-width:400px;">
    <h2>Inloggen</h2>
    <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
    <form method="post">
        <label>Gebruikersnaam</label>
        <input type="text" name="username" required>
        <label>Wachtwoord</label>
        <input type="password" name="password" required>
        <button class="btn" type="submit">Inloggen</button>
    </form>
    <p>Nog geen account? <a href="register.php">Registreer</a></p>
</div>
</body>
</html> 