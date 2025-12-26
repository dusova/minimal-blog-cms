<?php
declare(strict_types=1);

/** @var string $pageTitle */
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'Yönetim Paneli'); ?> - Mustafa Arda Düşova</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <div>
            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Yönetim Paneli</p>
            <h1 class="text-lg font-semibold">Mustafa Arda Düşova - Blog</h1>
        </div>
        <nav class="flex items-center gap-5 text-sm font-medium text-slate-600">
            <a class="transition hover:text-indigo-600" href="index.php">Yönetim Paneli</a>
            <a class="transition hover:text-indigo-600" href="posts.php">Yazıları Yönet</a>
            <a class="transition hover:text-indigo-600" href="categories.php">Kategorileri Yönet</a>
            <a class="transition hover:text-indigo-600" href="../index.php">Siteyi Gör</a>
            <a class="rounded-lg border border-slate-200 px-3 py-1 text-sm transition hover:border-indigo-200 hover:text-indigo-600" href="logout.php">Çıkış Yap</a>
        </nav>
    </div>
</header>
<main class="mx-auto max-w-6xl px-6 py-10">