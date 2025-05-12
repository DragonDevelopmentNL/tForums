<?php
session_start();
require 'includes/db.php';
require 'includes/lang.php';
if (!isset($_SESSION['user_id'])) die('Log eerst in.');
$res = $mysqli->query("SELECT language FROM settings WHERE id=1");
$settings = $res->fetch_assoc();
$lang = $settings['language'] ?? 'en';
$t = $langs[$lang];
$user_id = $_SESSION['user_id'];
$user = $mysqli->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $avatar = $user['avatar'];
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png'])) {
            if ($_FILES['avatar']['size'] <= 2*1024*1024) {
                $filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
                if (!is_dir('uploads')) mkdir('uploads');
                move_uploaded_file($_FILES['avatar']['tmp_name'], 'uploads/' . $filename);
                $avatar = $filename;
            } else {
                $error = 'Bestand te groot (max 2MB).';
            }
        } else {
            $error = 'Alleen jpg en png toegestaan.';
        }
    }
    if (!$error) {
        $stmt = $mysqli->prepare('UPDATE users SET email=?, bio=?, avatar=? WHERE id=?');
        $stmt->bind_param('sssi', $email, $bio, $avatar, $user_id);
        $stmt->execute();
        $success = 'Profiel bijgewerkt!';
        $user = $mysqli->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
    }
}
$avatar_url = $user['avatar'] ? 'uploads/' . htmlspecialchars($user['avatar']) : 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user['email']))) . '?d=mp';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title>Profiel</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .profile-topbar { background: #fff; border-bottom: 1px solid #ececec; padding: 10px 0; display: flex; gap: 24px; align-items: center; margin-bottom: 24px; }
        .profile-topbar a { color: #e76f51; text-decoration: none; font-weight: 500; margin-left: 32px; }
        .profile-topbar a:hover { text-decoration: underline; }
        .profile-box { max-width: 500px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; padding: 32px; }
        .profile-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 16px; }
    </style>
</head>
<body>
<div class="profile-topbar">
    <a href="profile.php">Profiel</a>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="admin.php">Admin</a>
    <?php endif; ?>
    <a href="index.php">&larr; <?php echo $t['back']; ?></a>
</div>
<div class="profile-box">
    <h2>Profiel</h2>
    <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <img class="profile-avatar" src="<?php echo $avatar_url; ?>" alt="avatar">
        <label><?php echo $t['email']; ?></label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <label>Bio</label>
        <textarea name="bio" rows="4"><?php echo htmlspecialchars($user['bio']); ?></textarea>
        <label>Profielfoto (jpg/png, max 2MB)</label>
        <input type="file" name="avatar" accept="image/*">
        <button class="btn" type="submit">Opslaan</button>
    </form>
</div>
</body>
</html> 