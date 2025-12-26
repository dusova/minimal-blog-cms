<?php
declare(strict_types=1); require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php'; start_session();
require_login(); if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('posts.php');
} $token = $_POST['csrf_token'] ?? '';
if (!verify_csrf($token)) { flash('Geçersiz istek.'); redirect('posts.php');
} $id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) { flash('Geçersiz yazı.'); redirect('posts.php');
} $stmt = $pdo->prepare('SELECT featured_image FROM posts WHERE id = :id');
$stmt->execute(['id' => $id]);
$post = $stmt->fetch(); $stmt = $pdo->prepare('DELETE FROM posts WHERE id = :id');
$stmt->execute(['id' => $id]); if ($post && !empty($post['featured_image'])) { $path = __DIR__ . '/../' . $post['featured_image']; if (is_file($path)) { unlink($path); }
} flash('Yazı silindi.');
redirect('posts.php');

