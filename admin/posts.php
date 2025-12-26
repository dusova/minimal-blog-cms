<?php
declare(strict_types=1);

require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';

start_session();
require_login();

$pageTitle = 'Yazılar';
$message = flash();

$stmt = $pdo->query(
    'SELECT p.id, p.title, p.status, p.created_at, c.name AS category_name
     FROM posts p
     LEFT JOIN categories c ON c.id = p.category_id
     ORDER BY p.created_at DESC'
);
$posts = $stmt->fetchAll();

require __DIR__ . '/header.php';
?>
<section class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold">Yazılar</h2>
            <p class="text-sm text-slate-600">Yayınlanan ve taslak yazıları yönet.</p>
        </div>
        <a href="post-create.php" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
            Yeni Yazı
        </a>
    </div>

    <?php if ($message): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            <?= e($message); ?>
        </div>
    <?php endif; ?>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-500">
                <tr>
                    <th class="px-6 py-4">Başlık</th>
                    <th class="px-6 py-4">Kategori</th>
                    <th class="px-6 py-4">Durum</th>
                    <th class="px-6 py-4">Tarih</th>
                    <th class="px-6 py-4 text-right">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (!$posts): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-slate-500">Henüz yazı yok.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td class="px-6 py-4 font-medium"><?= e($post['title']); ?></td>
                            <td class="px-6 py-4 text-slate-600"><?= e($post['category_name'] ?? 'Kategorisiz'); ?></td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold <?= $post['status'] === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'; ?>">
                                    <?= $post['status'] === 'published' ? 'Yayında' : 'Taslak'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                <?= e(date('d.m.Y', strtotime($post['created_at']))); ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a class="text-sm font-medium text-indigo-600 hover:text-indigo-700" href="post-edit.php?id=<?= (int) $post['id']; ?>">Düzenle</a>
                                <form method="post" action="post-delete.php" class="ml-4 inline" onsubmit="return confirm('Bu yazıyı silmek istiyor musun?');">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                                    <input type="hidden" name="id" value="<?= (int) $post['id']; ?>">
                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">Sil</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>