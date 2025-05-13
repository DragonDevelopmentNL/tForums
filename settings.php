<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die('Geen toegang.');
require 'includes/db.php';
require 'includes/lang.php';

$error = '';
$success = '';

// Get current settings
$settings = $mysqli->query("SELECT * FROM settings WHERE id=1")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $forum_name = trim($_POST['forum_name'] ?? '');
    $forum_description = trim($_POST['forum_description'] ?? '');
    $footer_text = trim($_POST['footer_text'] ?? '');
    $logo_url = trim($_POST['logo_url'] ?? '');
    
    if ($forum_name) {
        $stmt = $mysqli->prepare('UPDATE settings SET forum_name=?, forum_description=?, logo_url=?, footer_text=? WHERE id=1');
        $stmt->bind_param('ssss', $forum_name, $forum_description, $logo_url, $footer_text);
        
        if ($stmt->execute()) {
            $success = 'Instellingen zijn opgeslagen.';
            $settings = $mysqli->query("SELECT * FROM settings WHERE id=1")->fetch_assoc();
        } else {
            $error = 'Er is een fout opgetreden bij het opslaan van de instellingen.';
        }
    } else {
        $error = 'Forum naam is verplicht.';
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Forum Instellingen</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .settings-form { max-width: 600px; }
        .settings-form label { display: block; margin: 12px 0 4px; }
        .settings-form input[type="text"], 
        .settings-form textarea { 
            width: 100%; 
            padding: 8px;
            margin-bottom: 12px;
        }
        .settings-form textarea { 
            min-height: 100px;
        }
        .preview-logo {
            max-width: 200px;
            max-height: 100px;
            margin: 10px 0;
        }
        .success { color: #2a9d8f; margin-bottom: 16px; }
    </style>
</head>
<body>
<div class="admin-topbar">
    <a href="admin_forums.php">Forums beheren</a>
    <a href="admin_users.php">Leden beheren</a>
    <a href="settings.php">Instellingen</a>
    <a href="index.php">&larr; Terug</a>
</div>

<div class="container">
    <h2>Forum Instellingen</h2>
    
    <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
    
    <form method="post" class="settings-form">
        <label>Forum Naam</label>
        <input type="text" name="forum_name" value="<?php echo htmlspecialchars($settings['forum_name']); ?>" required>
        
        <label>Forum Omschrijving</label>
        <textarea name="forum_description"><?php echo htmlspecialchars($settings['forum_description']); ?></textarea>
        
        <label>Logo URL</label>
        <input type="text" name="logo_url" value="<?php echo htmlspecialchars($settings['logo_url'] ?? ''); ?>" placeholder="https://example.com/logo.png">
        <?php if (!empty($settings['logo_url'])): ?>
            <div>
                <p>Huidig logo:</p>
                <img src="<?php echo htmlspecialchars($settings['logo_url']); ?>" alt="Forum logo" class="preview-logo">
            </div>
        <?php endif; ?>
        
        <label>Footer Tekst</label>
        <textarea name="footer_text"><?php echo htmlspecialchars($settings['footer_text'] ?? ''); ?></textarea>
        
        <button class="btn" type="submit">Opslaan</button>
    </form>
</div>
</body>
</html> 