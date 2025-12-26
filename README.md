# dusova/minimal-blog-cms
**Minimal Blog CMS (PHP + MySQL)**  
İçerik Yönetim Sistemleri dersi kapsamında geliştirilmiş, PHP ve MySQL tabanlı minimal bir blog içerik yönetim sistemidir. Proje, hazır CMS altyapıları (WordPress vb.) kullanılmadan, bir CMS’in temel çalışma mantığını **sıfırdan** uygulamayı hedefler.

> Bu README, “rapor niyetine” hazırlanmıştır: Kurulum, mimari, güvenlik, veritabanı ve kullanım senaryolarını detaylı anlatır.

---

## İçindekiler
- [Özellikler](#özellikler)
- [Ekranlar ve Akış](#ekranlar-ve-akış)
- [Kullanılan Teknolojiler](#kullanılan-teknolojiler)
- [Gereksinimler](#gereksinimler)
- [Kurulum](#kurulum)
  - [1) Projeyi indirme](#1-projeyi-indirme)
  - [2) Web sunucusuna yerleştirme](#2-web-sunucusuna-yerleştirme)
  - [3) Veritabanını oluşturma](#3-veritabanını-oluşturma)
  - [4) Veritabanı bağlantı ayarları](#4-veritabanı-bağlantı-ayarları)
  - [5) İlk admin kullanıcı oluşturma (Varsayılan admin yok)](#5-i̇lk-admin-kullanıcı-oluşturma-varsayılan-admin-yok)
  - [6) Uygulamayı çalıştırma](#6-uygulamayı-çalıştırma)
  - [7) Sık karşılaşılan kurulum sorunları](#7-sık-karşılaşılan-kurulum-sorunları)
- [Proje Mimarisi](#proje-mimarisi)
  - [Katmanlar](#katmanlar)
  - [Dosya yapısı](#dosya-yapısı)
  - [İş kuralları](#i̇ş-kuralları)
- [Veritabanı Tasarımı](#veritabanı-tasarımı)
  - [Tablolar](#tablolar)
  - [Örnek sorgular](#örnek-sorgular)
- [Admin Panel](#admin-panel)
  - [Login / Session](#login--session)
  - [CRUD İşlemleri](#crud-i̇şlemleri)
- [Frontend (Public)](#frontend-public)
- [Güvenlik](#güvenlik)
  - [SQL Injection’a karşı koruma](#sql-injectiona-karşı-koruma)
  - [Şifre güvenliği](#şifre-güvenliği)
  - [Session güvenliği](#session-güvenliği)
  - [Dosya yükleme güvenliği (varsa)](#dosya-yükleme-güvenliği-varsa)
- [Geliştirme Notları](#geliştirme-notları)
- [Geliştirme Fikirleri](#geliştirme-fikirleri)
- [Lisans](#lisans)
- [Geliştirici](#geliştirici)

---

## Özellikler

- **Admin paneli** üzerinden içerik yönetimi
- **Login / Logout** (Session tabanlı)
- Blog içerikleri için **CRUD**
  - Create (Ekle)
  - Read (Listele/Görüntüle)
  - Update (Düzenle)
  - Delete (Sil)
- **Kategori** desteği (içeriklerin sınıflandırılması)
- **PDO** ile güvenli veritabanı işlemleri (Prepared Statement)
- Modüler, okunabilir, sade dosya organizasyonu (ders projelerine uygun)

---

## Ekranlar ve Akış

**Ziyaretçi (Public)**
1. `index.php`: İçerik listesi
2. İçeriğe tıklanınca `article.php`: İçerik detayı

**Admin**
1. `/admin/login.php`: Giriş
2. `/admin/...`: İçerik yönetimi (liste, ekle, düzenle, sil)

---

## Kullanılan Teknolojiler

- **PHP**: Backend (Sunucu tarafı)
- **MySQL / MariaDB**: Veritabanı
- **PDO**: DB bağlantısı + sorgular (prepared statements)
- **HTML**: Temel arayüz
- (Opsiyonel) CSS: Basit tema / düzen

---

## Gereksinimler

Önerilen minimum ortam:

- PHP **8.0+**
- MySQL **5.7+** veya MariaDB
- Apache / Nginx
- XAMPP / WAMP / Laragon (lokal geliştirme için önerilir)
- phpMyAdmin (kolay DB import için opsiyonel)

> PHP 7.x ile de çalışabilir; ancak `password_hash`, `password_verify` gibi fonksiyonlar için 8.x önerilir.

---

# Kurulum

## 1) Projeyi indirme

### Git ile klonlama
```bash
git clone https://github.com/dusova/minimal-blog-cms.git
````

### ZIP indirerek

GitHub üzerinden ZIP indirip açabilirsiniz.

---

## 2) Web sunucusuna yerleştirme

Proje klasörünü web sunucunuzun kök dizinine taşıyın.

### XAMPP örneği (Windows)

```text
C:\xampp\htdocs\minimal-blog-cms
```

### Laragon örneği

```text
C:\laragon\www\minimal-blog-cms
```

---

## 3) Veritabanını oluşturma

1. phpMyAdmin’i açın
2. Yeni veritabanı oluşturun:

```text
DB Name: minimal_blog_cms
Collation: utf8mb4_unicode_ci (önerilir)
```

3. Proje içindeki `schema.sql` dosyasını import edin:

* phpMyAdmin → Import → `schema.sql` seç → Go

---

## 4) Veritabanı bağlantı ayarları

`includes/db.php` dosyasında veritabanı bilgilerini kendi ortamınıza göre düzenleyin.

Örnek (tipik lokal ayar):

```php
$host = 'localhost';
$db   = 'minimal_blog_cms';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
```

> Eğer XAMPP’te root şifreniz varsa `$pass` alanına yazın.

---

## 5) İlk admin kullanıcı oluşturma (Varsayılan admin yok)

Bu projede **varsayılan admin kullanıcı** bulunmamaktadır.
Güvenlik açısından ilk admin kullanıcı kurulum sırasında manuel olarak oluşturulur.

### 5.1) Şifre hash üretme

Şifreler düz metin tutulmaz. BCRYPT ile hashlenir.

Aşağıdaki PHP kodunu çalıştırarak hash üretin:

```php
<?php 
$password = 'gokhanDogan123!'; 
$hashedPassword = password_hash($password, PASSWORD_BCRYPT); 
echo $hashedPassword; 
?>
```

* Bu çıktıyı kopyalayın (örnek: `$2y$10$...` ile başlar)
* Bir kez üretmeniz yeterlidir.

> Çalıştırma yöntemi:
>
> * Basitçe `hash.php` diye bir dosya oluşturup içine koyabilir,
> * Tarayıcıdan `http://localhost/hash.php` şeklinde açıp çıktıyı alabilirsiniz.

---

### 5.2) Users tablosuna admin ekleme

phpMyAdmin (SQL sekmesi) üzerinden aşağıdaki sorguyu çalıştırın:

```sql
INSERT INTO users (full_name, email, password_hash, role, created_at)
VALUES ('Gökhan Doğan', 'gokhan.dogan@klu.edu.tr', 'HASHLI_SIFRE', 'admin', NOW());
```

* `HASHLI_SIFRE` kısmını, 5.1’de aldığınız hash ile değiştirin.

**Örnek:**

```sql
INSERT INTO users (full_name, email, password_hash, role, created_at)
VALUES (
  'Gökhan Doğan',
  'gokhan.dogan@klu.edu.tr',
  '$2y$10$abc...xyz',
  'admin',
  NOW()
);
```

---

## 6) Uygulamayı çalıştırma

### Public

```text
http://localhost/minimal-blog-cms/
```

### Admin

```text
http://localhost/minimal-blog-cms/admin
```

---

## 7) Sık karşılaşılan kurulum sorunları

### “Database connection failed” / “Access denied”

* `includes/db.php` içindeki kullanıcı/şifre doğru mu?
* MySQL servisiniz çalışıyor mu?
* DB adı (`minimal_blog_cms`) birebir aynı mı?

### “Table 'users' doesn't exist”

* `schema.sql` import edilmemiş olabilir. Import’u tekrar kontrol edin.

### URL hatalı açılıyor / 404

* Proje klasörü `htdocs` altında doğru yerde mi?
* URL’de klasör adı doğru mu?

---

# Proje Mimarisi

## Katmanlar

### 1) Public (Ziyaretçi)

* İçerikleri listeler ve detay sayfası gösterir.
* Admin fonksiyonlarına erişmez.

### 2) Admin Panel

* Giriş gerektirir.
* İçerik yönetimi (CRUD) burada yapılır.

### 3) Includes

* DB bağlantısı ve ortak fonksiyonlar
* Tekrar eden kodların merkezi yönetimi

Bu yaklaşım, MVC’ye benzer bir ayrım sunar ancak ders projesi için sade tutulmuştur.

---

## Dosya yapısı

> Projedeki gerçek dosya adları farklıysa README içindeki isimleri birebir repo’ya göre güncelleyebilirsin. Mantık aynıdır.

```text
/
├── admin/
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── post-create.php
│   ├── post-edit.php
│   └── post-delete.php
│
├── includes/
│   ├── db.php
│   └── functions.php
│
├── uploads/               # varsa
├── index.php
├── article.php
└── schema.sql
```

---

## İş kuralları

* Public tarafta sadece yayınlanan içerikler listelenir (proje mantığına göre).
* Admin panelde:

  * içerik eklenir/düzenlenir/silinir
  * kategori seçilebilir
* Kullanıcı şifreleri:

  * **hash** olarak saklanır
  * girişte `password_verify()` ile doğrulanır

---

# Veritabanı Tasarımı

## Tablolar

> `schema.sql` içinde yer alan alan isimleri birebir farklı olabilir; mantık burada açıklanmıştır.

### `users`

Amaç: Admin kullanıcıların tutulması.

Örnek alanlar:

* `id` (PK)
* `full_name`
* `email` (unique önerilir)
* `password_hash`
* `role` (ör. `admin`)
* `created_at`

### `categories`

Amaç: İçerikleri kategorize etmek.

Örnek alanlar:

* `id` (PK)
* `name`
* `created_at`

### `posts`

Amaç: Blog içeriklerinin tutulması.

Örnek alanlar:

* `id` (PK)
* `title`
* `content`
* `category_id` (FK -> categories.id)
* `created_at`
* (opsiyonel) `updated_at`

---

## Örnek sorgular

### İçerikleri listeleme

```sql
SELECT p.id, p.title, p.created_at, c.name AS category
FROM posts p
LEFT JOIN categories c ON c.id = p.category_id
ORDER BY p.created_at DESC;
```

### Tek bir içeriği çekme

```sql
SELECT p.*, c.name AS category
FROM posts p
LEFT JOIN categories c ON c.id = p.category_id
WHERE p.id = ?
LIMIT 1;
```

---

# Admin Panel

## Login / Session

* Kullanıcı girişinde email + şifre alınır.
* DB’den ilgili user bulunur.
* `password_verify($plain, $hash)` ile doğrulama yapılır.
* Başarılıysa `$_SESSION` içine kullanıcı bilgisi yazılır.
* Admin sayfalarında session kontrolü yapılır.

**Neden Session?**

* HTTP stateless bir protokoldür; kullanıcı durumunu (login) tutmak için session kullanılır.

---

## CRUD İşlemleri

### Create (Ekle)

* Admin panelde form ile başlık, içerik, kategori alınır.
* `INSERT` ile DB’ye kaydedilir.

### Read (Listele)

* Admin panelde içerikler listelenir.
* Public tarafta içerik listesi gösterilir.

### Update (Düzenle)

* Seçilen içerik DB’den çekilir.
* Formda güncellenir.
* `UPDATE` ile kaydedilir.

### Delete (Sil)

* İçerik id’ye göre silinir.
* `DELETE FROM posts WHERE id = ?`

---

# Frontend (Public)

* `index.php` içerikleri listeler.
* Her içerik tıklanınca `article.php?id=...` veya route mantığıyla detay sayfası açılır.
* Public tarafta admin fonksiyonları bulunmaz.

---

# Güvenlik

## SQL Injection’a karşı koruma

Veritabanı işlemleri **PDO Prepared Statement** ile yapılır.
Bu yaklaşım:

* Kullanıcı girdisini SQL komutundan ayırır
* Sorgu enjekte edilmesini engeller

Örnek mantık:

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

## Şifre güvenliği

* Şifreler düz metin tutulmaz.
* `password_hash(..., PASSWORD_BCRYPT)` ile hashlenir.
* Login sırasında `password_verify` ile doğrulanır.

Bu yöntem:

* Rainbow table saldırılarına karşı daha güvenlidir
* BCRYPT maliyet (cost) parametresiyle brute-force’u zorlaştırır

## Session güvenliği

Öneriler:

* Giriş sonrası session regenerate (varsa)
* Logout’ta session destroy
* Admin sayfalarında session check zorunluluğu

## Dosya yükleme güvenliği (varsa)

Eğer proje dosya yükleme destekliyorsa:

* Upload edilen dosya tipi doğrulanmalı (MIME + uzantı)
* Upload dizininde PHP çalıştırılması engellenmeli (`.htaccess`)

---

# Geliştirme Notları

Bu proje minimal tutulmuştur. Ama mimari, “sonradan büyütülebilir” şekilde planlanmıştır:

* Ortak işlemler `includes/` altında toplanır
* Admin / Public ayrımı nettir
* DB erişimi tek yerden yönetilir

---

# Geliştirme Fikirleri

* Rol bazlı yetki sistemi (admin/editor)
* Yorum sistemi
* Etiketleme (tags)
* Medya kütüphanesi
* API (REST) ile dışarı açılma
* CSRF token, rate limiting gibi ekstra güvenlik katmanları

---

# Lisans

Bu proje eğitim amaçlı geliştirilmiştir. [MIT LICENSE](LICENSE)

---

# Geliştirici

**Mustafa Arda Düşova**
Bilgisayar Programcılığı – İçerik Yönetim Sistemleri Dersi
