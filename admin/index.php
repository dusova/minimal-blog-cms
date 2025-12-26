<?php
declare(strict_types=1);

require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';

start_session();
require_login();

$pageTitle = 'Yönetim Paneli';

$totalPosts = (int) $pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
$publishedPosts = (int) $pdo->query('SELECT COUNT(*) FROM posts WHERE status = "published"')->fetchColumn();
$draftPosts = (int) $pdo->query('SELECT COUNT(*) FROM posts WHERE status = "draft"')->fetchColumn();
$totalCategories = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();

require __DIR__ . '/header.php';
?>
<section class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold">Tekrar hoş geldin, <?= e($_SESSION['user_name'] ?? 'Yönetici'); ?>.</h2>
            <p class="text-sm text-slate-600">İçeriklerin için hızlı bir özet.</p>
        </div>
        <a href="post-create.php" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
            Yeni Yazı
        </a>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Toplam Yazı</p>
            <p class="mt-4 text-3xl font-semibold"><?= $totalPosts; ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Yayında</p>
            <p class="mt-4 text-3xl font-semibold"><?= $publishedPosts; ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Taslak</p>
            <p class="mt-4 text-3xl font-semibold"><?= $draftPosts; ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Kategoriler</p>
            <p class="mt-4 text-3xl font-semibold"><?= $totalCategories; ?></p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <h3 class="text-lg font-semibold">Sonraki Adımlar</h3>
        <p class="mt-2 text-sm text-slate-600">Blogu düzenli içeriklerle canlı tut.</p>
        <div class="mt-4 flex flex-wrap gap-3 text-sm">
            <a href="posts.php" class="rounded-lg border border-slate-200 px-3 py-2 transition hover:text-indigo-600">Yazıları Yönet</a>
            <a href="categories.php" class="rounded-lg border border-slate-200 px-3 py-2 transition hover:text-indigo-600">Kategorileri Yönet</a>
            <a href="post-create.php" class="rounded-lg border border-slate-200 px-3 py-2 transition hover:text-indigo-600">Yeni Yazı Oluştur</a>
        </div>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>