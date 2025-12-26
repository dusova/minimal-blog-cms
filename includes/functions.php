<?php
declare(strict_types=1); function start_session(): void
{ if (session_status() === PHP_SESSION_NONE) { session_set_cookie_params([ 'lifetime' => 0, 'path' => '/', 'httponly' => true, 'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'samesite' => 'Lax', ]); session_start(); }
} function e(string $value): string
{ return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
} function csrf_token(): string
{ start_session(); if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); } return $_SESSION['csrf_token'];
} function verify_csrf(?string $token): bool
{ start_session(); return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
} function is_logged_in(): bool
{ start_session(); return isset($_SESSION['user_id']);
} function require_login(string $redirect = 'login.php'): void
{ if (!is_logged_in()) { header('Location: ' . $redirect); exit; }
} function redirect(string $url): void
{ header('Location: ' . $url); exit;
} function slugify(string $text): string
{ $text = trim($text); if ($text === '') { return 'post'; } $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text); if ($transliterated !== false) { $text = $transliterated; } $text = strtolower($text); $text = preg_replace('/[^a-z0-9]+/', '-', $text); $text = trim($text, '-'); return $text !== '' ? $text : 'post';
} function flash(?string $message = null): ?string
{ start_session(); if ($message !== null) { $_SESSION['flash_message'] = $message; return null; } if (isset($_SESSION['flash_message'])) { $value = $_SESSION['flash_message']; unset($_SESSION['flash_message']); return $value; } return null;
}

