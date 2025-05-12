<?php
// install.php
session_start();
if (file_exists('includes/db.php')) {
    die('Forum is al geïnstalleerd. Verwijder includes/db.php om opnieuw te installeren.');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'] ?? '';
    $db_user = $_POST['db_user'] ?? '';
    $db_pass = $_POST['db_pass'] ?? '';
    $db_name = $_POST['db_name'] ?? '';
    $forum_name = $_POST['forum_name'] ?? '';
    $forum_desc = $_POST['forum_desc'] ?? '';
    $language = $_POST['language'] ?? 'nl';
    $admin_user = $_POST['admin_user'] ?? '';
    $admin_pass = $_POST['admin_pass'] ?? '';
    $admin_email = $_POST['admin_email'] ?? '';

    if (!$db_host || !$db_user || !$db_name || !$forum_name || !$admin_user || !$admin_pass || !$admin_email) {
        $errors[] = 'Vul alle verplichte velden in.';
    } else {
        $mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);
        if ($mysqli->connect_errno) {
            $errors[] = 'Databaseverbinding mislukt: ' . $mysqli->connect_error;
        } else {
            // Tabellen aanmaken
            $queries = [
                "CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50) UNIQUE, password VARCHAR(255), email VARCHAR(100), role ENUM('admin','moderator','user') DEFAULT 'user', banned TINYINT(1) DEFAULT 0)",
                "CREATE TABLE IF NOT EXISTS forums (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), description TEXT)",
                "CREATE TABLE IF NOT EXISTS threads (id INT AUTO_INCREMENT PRIMARY KEY, forum_id INT, user_id INT, title VARCHAR(255), closed TINYINT(1) DEFAULT 0, announcement TINYINT(1) DEFAULT 0, created_at DATETIME, FOREIGN KEY (forum_id) REFERENCES forums(id), FOREIGN KEY (user_id) REFERENCES users(id))",
                "CREATE TABLE IF NOT EXISTS posts (id INT AUTO_INCREMENT PRIMARY KEY, thread_id INT, user_id INT, content TEXT, created_at DATETIME, updated_at DATETIME, FOREIGN KEY (thread_id) REFERENCES threads(id), FOREIGN KEY (user_id) REFERENCES users(id))",
                "CREATE TABLE IF NOT EXISTS settings (id INT PRIMARY KEY, forum_name VARCHAR(100), forum_description TEXT, language VARCHAR(10) NOT NULL DEFAULT 'nl')"
            ];
            foreach ($queries as $q) {
                if (!$mysqli->query($q)) {
                    $errors[] = 'Fout bij aanmaken tabellen: ' . $mysqli->error;
                }
            }
            // Admin user aanmaken
            $hash = password_hash($admin_pass, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')");
            $stmt->bind_param('sss', $admin_user, $hash, $admin_email);
            if (!$stmt->execute()) {
                $errors[] = 'Fout bij aanmaken admin: ' . $stmt->error;
            }
            // Settings opslaan
            $stmt2 = $mysqli->prepare("INSERT INTO settings (id, forum_name, forum_description, language) VALUES (1, ?, ?, ?)");
            $stmt2->bind_param('sss', $forum_name, $forum_desc, $language);
            if (!$stmt2->execute()) {
                $errors[] = 'Fout bij opslaan instellingen: ' . $stmt2->error;
            }
            // db.php aanmaken
            if (empty($errors)) {
                if (!is_dir('includes')) mkdir('includes');
                $dbfile = fopen('includes/db.php', 'w');
                $dbcode = "<?php\n$mysqli = new mysqli('$db_host', '$db_user', '$db_pass', '$db_name');\nif (
$mysqli->connect_errno) { die('Database connectie mislukt: ' . $mysqli->connect_error); }\n?>";
                fwrite($dbfile, $dbcode);
                fclose($dbfile);
                $success = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Forum Installatie</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f8fa; }
        .install-box { max-width: 400px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; padding: 32px; }
        h2 { color: #e76f51; }
        label { display: block; margin-top: 16px; }
        input, textarea, select { width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #e76f51; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; margin-top: 20px; cursor: pointer; }
        .error { color: #c00; margin-top: 10px; }
        .success { color: #080; margin-top: 10px; }
    </style>
</head>
<body>
<div class="install-box">
    <h2>Forum Installatie</h2>
    <?php if ($success): ?>
        <div class="success">Installatie voltooid! Verwijder install.php voor veiligheid.<br><a href="index.php">Ga naar het forum</a></div>
    <?php else: ?>
        <?php foreach ($errors as $e): ?><div class="error"><?php echo $e; ?></div><?php endforeach; ?>
        <form method="post">
            <label>Database host *</label>
            <input type="text" name="db_host" required value="localhost">
            <label>Database gebruiker *</label>
            <input type="text" name="db_user" required>
            <label>Database wachtwoord</label>
            <input type="password" name="db_pass">
            <label>Database naam *</label>
            <input type="text" name="db_name" required>
            <label>Forum naam *</label>
            <input type="text" name="forum_name" required>
            <label>Forum omschrijving</label>
            <textarea name="forum_desc"></textarea>
            <label>Taal *</label>
            <select name="language">
                <option value="nl">Nederlands</option>
                <option value="en">English</option>
            </select>
            <label>Admin gebruikersnaam *</label>
            <input type="text" name="admin_user" required>
            <label>Admin wachtwoord *</label>
            <input type="password" name="admin_pass" required>
            <label>Admin e-mail *</label>
            <input type="email" name="admin_email" required>
            <button type="submit">Installeer</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html> 