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

// Get forum_id before deleting anything
$forum_id = $mysqli->query("SELECT forum_id FROM threads WHERE id=$thread_id")->fetch_assoc()['forum_id'];

// Start transaction
$mysqli->begin_transaction();
try {
    // Delete the post
    $mysqli->query("DELETE FROM posts WHERE id=$id");
    
    // Check if this was the last post in the thread
    $remaining_posts = $mysqli->query("SELECT COUNT(*) as count FROM posts WHERE thread_id=$thread_id")->fetch_assoc();
    
    if ($remaining_posts['count'] == 0) {
        // If it was the last post, delete the thread
        $mysqli->query("DELETE FROM threads WHERE id=$thread_id");
    }
    
    $mysqli->commit();
    header('Location: forum.php?id=' . $forum_id);
} catch (Exception $e) {
    $mysqli->rollback();
    die('Er is een fout opgetreden bij het verwijderen van het bericht.');
}
exit; 