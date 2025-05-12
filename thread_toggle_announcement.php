<?php
require_once 'includes/db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die('Geen toegang.');
$thread_id = intval($_POST['thread_id'] ?? 0);
$action = $_POST['action'] ?? '';
if ($thread_id && in_array($action, ['make','remove'])) {
    $announcement = ($action === 'make') ? 1 : 0;
    $stmt = $mysqli->prepare('UPDATE threads SET announcement=? WHERE id=?');
    $stmt->bind_param('ii', $announcement, $thread_id);
    $stmt->execute();
}
header('Location: thread.php?id=' . $thread_id);
exit; 