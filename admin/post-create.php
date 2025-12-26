<?php
declare(strict_types=1);

require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';

start_session();
require_login();

$pageTitle = 'Yazı Oluştur';
$error = '';

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC')->fetchAll();

$post = [
    'title' => '',
    'slug' => '',
    'excerpt' => '',
    'content' => '',
    'status' => 'draft',
    'category_id' => '',
    'featured_image' => null,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        $error = 'Geçersiz istek.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $slugInput = trim($_POST['slug'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status = $_POST['status'] ?? 'draft';
        $categoryId = (int) ($_POST['category_id'] ?? 0);

        if ($title === '' || $content === '') {
            $error = 'Başlık ve içerik zorunludur.';
        } elseif (!in_array($status, ['draft', 'published'], true)) {
            $error = 'Geçersiz durum.';
        } else {
            $slugBase = $slugInput !== '' ? $slugInput : $title;
            $slug = slugify($slugBase);
            $uniqueSlug = $slug;
            $counter = 2;
            $slugCheck = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE slug = :slug');
            while (true) {
                $slugCheck->execute(['slug' => $uniqueSlug]);
                if ((int) $slugCheck->fetchColumn() === 0) {
                    break;
                }
                $uniqueSlug = $slug . '-' . $counter;
                $counter++;
            }

            $featuredImage = null;
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                    $maxSize = 2 * 1024 * 1024;
                    if ($_FILES['featured_image']['size'] > $maxSize) {
                        $error = 'Görsel 2MB boyutundan küçük olmalı.';
                    } else {
                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                        $mime = $finfo->file($_FILES['featured_image']['tmp_name']);
                        $allowed = [
                            'image/jpeg' => 'jpg',
                            'image/png' => 'png',
                            'image/webp' => 'webp',
                        ];
                        if (!isset($allowed[$mime])) {
                            $error = 'Sadece JPG, PNG veya WEBP görselleri kabul edilir.';
                        } else {
                            $uploadDir = __DIR__ . '/../uploads';
                            if (!is_dir($uploadDir)) {
                                mkdir($uploadDir, 0755, true);
                            }
                            $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
                            $destination = $uploadDir . '/' . $filename;
                            if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $destination)) {
                                $error = 'Görsel yükleme başarısız.';
                            } else {
                                $featuredImage = 'uploads/' . $filename;
                            }
                        }
                    }
                } else {
                    $error = 'Yükleme başarısız.';
                }
            }

            if ($error === '') {
                $stmt = $pdo->prepare(
                    'INSERT INTO posts (author_id, category_id, title, slug, excerpt, content, featured_image, status)
                     VALUES (:author_id, :category_id, :title, :slug, :excerpt, :content, :featured_image, :status)'
                );
                $stmt->execute([
                    'author_id' => (int) ($_SESSION['user_id'] ?? 0),
                    'category_id' => $categoryId > 0 ? $categoryId : null,
                    'title' => $title,
                    'slug' => $uniqueSlug,
                    'excerpt' => $excerpt !== '' ? $excerpt : null,
                    'content' => $content,
                    'featured_image' => $featuredImage,
                    'status' => $status,
                ]);
                flash('Yazı oluşturuldu.');
                redirect('posts.php');
            }
        }

        $post = [
            'title' => $title ?? '',
            'slug' => $slugInput ?? '',
            'excerpt' => $excerpt ?? '',
            'content' => $content ?? '',
            'status' => $status ?? 'draft',
            'category_id' => $categoryId ?? '',
            'featured_image' => $featuredImage ?? null,
        ];
    }
}

require __DIR__ . '/header.php';
?>
<section class="space-y-8">
    <div>
        <h2 class="text-2xl font-semibold">Yeni Yazı Oluştur</h2>
        <p class="text-sm text-slate-600">Blog için yeni bir yazı hazırla.</p>
    </div>

    <?php if ($error): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <?= e($error); ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
        <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700" for="title">Başlık</label>
                    <input id="title" name="title" value="<?= e($post['title']); ?>" required class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700" for="slug">Kısaltma (opsiyonel)</label>
                    <input id="slug" name="slug" value="<?= e($post['slug']); ?>" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700" for="excerpt">Özet</label>
                    <textarea id="excerpt" name="excerpt" rows="3" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"><?= e($post['excerpt']); ?></textarea>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700" for="content">İçerik</label>
                    <textarea id="content" name="content" rows="12" required class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"><?= e($post['content']); ?></textarea>
                </div>
            </div>
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700" for="status">Durum</label>
                    <select id="status" name="status" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : ''; ?>>Taslak</option>
                        <option value="published" <?= $post['status'] === 'published' ? 'selected' : ''; ?>>Yayında</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700" for="category_id">Kategori</label>
                    <select id="category_id" name="category_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        <option value="">Kategorisiz</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id']; ?>" <?= (string) $post['category_id'] === (string) $category['id'] ? 'selected' : ''; ?>>
                                <?= e($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700" for="featured_image">Kapak Görseli</label>
                    <input id="featured_image" name="featured_image" type="file" accept="image/*" class="w-full rounded-xl border border-dashed border-slate-200 px-4 py-3 text-sm text-slate-600">
                    <p class="text-xs text-slate-500">JPG, PNG veya WEBP - 2MB sınırı.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                Kaydet
            </button>
            <a href="posts.php" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:text-indigo-600">
                İptal
            </a>
        </div>
    </form>
</section>
<?php require __DIR__ . '/footer.php'; ?>