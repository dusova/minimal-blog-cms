<?php
declare(strict_types=1);

require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';

start_session();

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        $error = 'Geçersiz istek. Lütfen tekrar deneyin.';
    } else {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!$email || $password === '') {
            $error = 'Geçerli bir e-posta ve şifre girin.';
        } else {
            $stmt = $pdo->prepare('SELECT id, full_name, password_hash FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                redirect('index.php');
            }

            $error = 'E-posta veya şifre hatalı.';
        }
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yönetici Girişi - Mustafa Arda Düşova</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <main class="mx-auto flex min-h-screen max-w-md items-center px-6">
        <form method="post" class="w-full space-y-6 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
            <div class="space-y-2">
                <h1 class="text-2xl font-semibold">Yönetici Girişi</h1>
                <p class="text-sm text-slate-600">Yazıları yönetmek için giriş yapın.</p>
            </div>

            <?php if ($error): ?>
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <?= e($error); ?>
                </div>
            <?php endif; ?>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-700" for="email">E-posta</label>
                <input id="email" name="email" type="email" required class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-700" for="password">Şifre</label>
                <input id="password" name="password" type="password" required class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            </div>
            <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                Giriş Yap
            </button>
        </form>
    </main>
</body>
</html>