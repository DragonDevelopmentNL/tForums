<?php
require_once 'includes/db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die('Geen toegang.');
$thread_id = intval($_POST['thread_id'] ?? 0);
$action = $_POST['action'] ?? '';
if ($thread_id && in_array($action, ['close','open'])) {
    $closed = ($action === 'close') ? 1 : 0;
    $stmt = $mysqli->prepare('UPDATE threads SET closed=? WHERE id=?');
    $stmt->bind_param('ii', $closed, $thread_id);
    $stmt->execute();
}
header('Location: thread.php?id=' . $thread_id);
exit; 