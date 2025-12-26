<?php
declare(strict_types=1);

require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';

start_session();
require_login();

$pageTitle = 'Kategoriler';
$error = '';
$message = flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        $error = 'Gecersiz istek.';
    } elseif (isset($_POST['action']) && $_POST['action'] === 'create') {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $error = 'Kategori adı zorunludur.';
        } else {
            $slug = slugify($name);
            $stmt = $pdo->prepare('INSERT INTO categories (name, slug) VALUES (:name, :slug)');
            try {
                $stmt->execute(['name' => $name, 'slug' => $slug]);
                flash('Kategori eklendi.');
                redirect('categories.php');
            } catch (PDOException $e) {
                $error = 'Bu kategori zaten var.';
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
            $stmt->execute(['id' => $id]);
            flash('Kategori silindi.');
            redirect('categories.php');
        }
        $error = 'Geçersiz kategori.';
    }
}

$categories = $pdo->query('SELECT id, name, slug FROM categories ORDER BY name ASC')->fetchAll();

require __DIR__ . '/header.php';
?>
<section class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold">Kategoriler</h2>
            <p class="text-sm text-slate-600">Yazıları tutarlı başlıklar altında topla.</p>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            <?= e($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <?= e($error); ?>
        </div>
    <?php endif; ?>

    <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="text-lg font-semibold">Mevcut Kategoriler</h3>
            <div class="mt-4 divide-y divide-slate-100">
                <?php if (!$categories): ?>
                    <p class="py-6 text-sm text-slate-500">Henüz kategori yok.</p>
                <?php else: ?>
                    <?php foreach ($categories as $category): ?>
                        <div class="flex items-center justify-between py-4">
                            <div>
                                <p class="font-medium"><?= e($category['name']); ?></p>
                                <p class="text-xs text-slate-500">Kısaltma: <?= e($category['slug']); ?></p>
                            </div>
                            <form method="post" onsubmit="return confirm('Bu kategoriyi silmek istiyor musun?');">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $category['id']; ?>">
                                <button type="submit" class="text-sm text-red-600 hover:text-red-700">Sil</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="text-lg font-semibold">Kategori Ekle</h3>
            <form method="post" class="mt-4 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                <input type="hidden" name="action" value="create">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700" for="name">Kategori Adı</label>
                    <input id="name" name="name" type="text" required class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                </div>
                <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                    Kaydet
                </button>
            </form>
        </div>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>