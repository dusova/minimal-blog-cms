<?php
declare(strict_types=1);

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$stmt = $pdo->query(
    'SELECT p.id, p.title, p.excerpt, p.created_at, p.featured_image, c.name AS category_name
     FROM posts p
     LEFT JOIN categories c ON c.id = p.category_id
     WHERE p.status = "published"
     ORDER BY p.created_at DESC'
);
$posts = $stmt->fetchAll();
?>
<?php require __DIR__ . '/includes/header.php'; ?>
<main class="mx-auto max-w-5xl px-6 py-16">
    <section class="mb-16">
        <p class="text-sm uppercase tracking-[0.25em] text-slate-500">notlar • düşünceler • parçalar</p>
        <h1 class="mt-6 text-4xl font-semibold leading-tight sm:text-5xl">
           Her şey yapılabilir.<br>
            Ama her şey yapılmamalı.
        </h1>
        <p class="mt-6 max-w-2xl text-lg text-slate-600">
            Kimi zaman teknik, kimi zaman kişisel. Ama her zaman dürüst.       
        </p>
    </section>

    <section class="space-y-8">
        <?php if (!$posts): ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-8">
                <p class="text-slate-600">Henüz yazı yok. Yakında tekrar kontrol edin.</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <article class="rounded-2xl border border-slate-200 bg-white p-8 transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center gap-3 text-xs uppercase tracking-[0.2em] text-slate-500">
                        <span><?= e($post['category_name'] ?? 'Kategorisiz'); ?></span>
                        <span class="h-px w-8 bg-slate-300"></span>
                        <time datetime="<?= e(date('Y-m-d', strtotime($post['created_at']))); ?>">
                            <?= e(date('d.m.Y', strtotime($post['created_at']))); ?>
                        </time>
                    </div>
                    <?php if (!empty($post['featured_image'])): ?>
                        <div class="mt-6 overflow-hidden rounded-xl border border-slate-100">
                            <img src="<?= e($post['featured_image']); ?>" alt="" class="h-48 w-full object-cover" loading="lazy">
                        </div>
                    <?php endif; ?>
                    <h2 class="mt-5 text-2xl font-semibold">
                        <a class="transition hover:text-indigo-600" href="article.php?id=<?= (int) $post['id']; ?>">
                            <?= e($post['title']); ?>
                        </a>
                    </h2>
                    <p class="mt-4 text-slate-600">
                        <?= e($post['excerpt'] ?? ''); ?>
                    </p>
                    <a class="mt-6 inline-flex items-center gap-2 text-sm font-medium text-indigo-600" href="article.php?id=<?= (int) $post['id']; ?>">
                        Yazının tamamını oku
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>
</body>
</html>