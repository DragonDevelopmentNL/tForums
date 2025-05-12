<?php
session_start();
if (isset($_SESSION['user_id'])) header('Location: index.php');
$error = '';
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'includes/db.php';
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$username || !$email || !$password) {
        $error = 'Vul alle velden in.';
    } else {
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE username=? OR email=?');
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Gebruikersnaam of e-mail bestaat al.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $mysqli->prepare('INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, "user")');
            $stmt2->bind_param('sss', $username, $hash, $email);
            if ($stmt2->execute()) {
                $success = true;
            } else {
                $error = 'Registratie mislukt.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Registreren</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container" style="max-width:400px;">
    <h2>Registreren</h2>
    <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?>
        <div class="success">Registratie gelukt! <a href="login.php">Log nu in</a>.</div>
    <?php else: ?>
    <form method="post">
        <label>Gebruikersnaam</label>
        <input type="text" name="username" required>
        <label>E-mail</label>
        <input type="email" name="email" required>
        <label>Wachtwoord</label>
        <input type="password" name="password" required>
        <button class="btn" type="submit">Registreren</button>
    </form>
    <p>Al een account? <a href="login.php">Log in</a></p>
    <?php endif; ?>
</div>
</body>
</html> 