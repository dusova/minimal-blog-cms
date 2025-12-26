<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(404);
    echo 'Yazı bulunamadı.';
    exit;
}

$stmt = $pdo->prepare(
    'SELECT p.title, p.content, p.created_at, p.featured_image, p.views, c.name AS category_name, u.full_name
     FROM posts p
     LEFT JOIN categories c ON c.id = p.category_id
     LEFT JOIN users u ON u.id = p.author_id
     WHERE p.id = :id AND p.status = "published"'
);
$stmt->execute(['id' => $id]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    echo 'Yazı bulunamadı.';
    exit;
}

$update = $pdo->prepare('UPDATE posts SET views = views + 1 WHERE id = :id');
$update->execute(['id' => $id]);
$post['views'] = (int) ($post['views'] ?? 0) + 1;

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$currentUrl = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '');
$shareUrl = urlencode($currentUrl);
$shareTitle = urlencode($post['title']);
?>
<?php require __DIR__ . '/includes/header.php'; ?>
<main class="mx-auto max-w-3xl px-6 py-16">
    <article class="space-y-8">
        <div class="space-y-4">
            <a href="index.php" class="inline-flex items-center gap-2 text-sm text-slate-500 transition hover:text-indigo-600">
                <span aria-hidden="true">&larr;</span>
                Geri Dön
            </a>
            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">
                <?= e($post['category_name'] ?? 'Kategorisiz'); ?>
            </p>
            <h1 class="text-4xl font-semibold leading-tight sm:text-5xl">
                <?= e($post['title']); ?>
            </h1>
            <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
                <span><?= e($post['full_name'] ?? 'Mustafa Arda Dusova'); ?></span>
                <span class="h-px w-8 bg-slate-300"></span>
                <time datetime="<?= e(date('Y-m-d', strtotime($post['created_at']))); ?>">
                    <?= e(date('d.m.Y', strtotime($post['created_at']))); ?>
                </time>
                <span class="h-px w-8 bg-slate-300"></span>
                <span><?= (int) $post['views']; ?> görüntülenme</span>
            </div>
        </div>

        <?php if (!empty($post['featured_image'])): ?>
            <div class="overflow-hidden rounded-2xl border border-slate-200">
                <img src="<?= e($post['featured_image']); ?>" alt="" class="h-72 w-full object-cover" loading="lazy">
            </div>
        <?php endif; ?>

        <div class="prose prose-slate max-w-none">
            <?= nl2br(e($post['content'] ?? '')); ?>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-semibold">Bu yazıyı paylaş</h2>
            <div class="mt-4 flex flex-wrap gap-3 text-sm">
                <a class="rounded-lg border border-slate-200 px-3 py-2 transition hover:text-indigo-600" href="https://twitter.com/intent/tweet?url=<?= $shareUrl; ?>&text=<?= $shareTitle; ?>" target="_blank" rel="noopener">X / Twitter</a>
                <a class="rounded-lg border border-slate-200 px-3 py-2 transition hover:text-indigo-600" href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl; ?>" target="_blank" rel="noopener">Facebook</a>
                <a class="rounded-lg border border-slate-200 px-3 py-2 transition hover:text-indigo-600" href="https://wa.me/?text=<?= $shareTitle; ?>%20<?= $shareUrl; ?>" target="_blank" rel="noopener">WhatsApp</a>
                <a class="rounded-lg border border-slate-200 px-3 py-2 transition hover:text-indigo-600" href="mailto:?subject=<?= $shareTitle; ?>&body=<?= $shareUrl; ?>">E-posta</a>
            </div>
        </div>
    </article>
</main>
</body>
</html>