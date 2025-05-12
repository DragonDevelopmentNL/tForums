<?php
require_once 'includes/db.php';
session_start();
$id = intval($_GET['id'] ?? 0);
$thread_id = intval($_GET['thread_id'] ?? 0);
$post = $mysqli->query("SELECT * FROM posts WHERE id=$id")->fetch_assoc();
if (!$post) die('Post niet gevonden.');
$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? '';
if (!($user_id == $post['user_id'] || in_array($role, ['admin','moderator']))) die('Geen toegang.');
$mysqli->query("DELETE FROM posts WHERE id=$id");
header('Location: thread.php?id=' . $thread_id);
exit; 