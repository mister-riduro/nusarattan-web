# Plan: Migrasi nusarattan.com (Framer) ke Laravel + CMS

> Pemilik situs: Ridlo (milik pribadi). Alasan migrasi: biaya Framer terlalu besar.
> Target: tampilan, animasi, dan behavior **1:1** dengan situs saat ini, kode rapi dan mudah dibaca, tanpa jejak Framer, dengan CMS untuk mengelola produk.

---

## 1. Keputusan yang sudah dikunci

| Topik | Keputusan |
|---|---|
| Framework | Laravel (Blade + Tailwind + Alpine.js, build dengan Vite) |
| Database | MySQL di hosting biasa |
| Gambar | Dikonversi ke **webp** di server, diunggah ke **Supabase Storage** |
| Yang disimpan di DB | **URL publik** gambar dari Supabase |
| CMS | **Filament** (hosting sudah mendukung) |
| Harga | Tampil di situs, dalam **USD** ("Starts from $48"), set furnitur punya harga coret |
| Fidelity | Desain, animasi, dan behavior 1:1 |
| Framer | Semua nama dan jejak Framer dihapus dari kode tanpa mengubah fungsi |

---

## 2. Stack

- PHP 8.2+, Laravel 11/12
- Tailwind CSS (design token dari hasil audit), Alpine.js, GSAP hanya bila CSS tidak cukup
- `league/flysystem-aws-s3-v3` (driver S3 untuk Supabase)
- `intervention/image` v3 (konversi webp)
- Filament (panel admin)
- Playwright + pixelmatch (uji 1:1)

---

## 3. Inventaris situs saat ini

| Area | Isi |
|---|---|
| Halaman | Home, Product (6 kategori), About Us, Contact (anchor `#contact`) |
| Kategori | Indoor Furniture, Outdoor Dining Set, Pool Area, Outdoor Living Set, Rattan Poles, Peacock Collection |
| URL kategori | `/product-indoor`, `/product-outdoor-dining`, `/product-pool-area`, `/product-outdoor-living`, `/product-rattan-poles`, `/product-peacock-collection` |
| Home | Hero + CTA "Download Catalog" dan "Watch our Video"; komponen Furniture Set bertab (sofa, table, arm-chair) dengan harga coret dan tombol WhatsApp; "Why Choose Nusarattan" (3 poin); Featured Products (8 item); banner + form kontak |
| Halaman kategori | Header + CTA katalog, tab kategori, daftar item (foto, nama, material), banner + form kontak |
| Global | Navbar, footer, peta Google Maps embed, copyright |
| Form kontak | Name, Email, Country, Message |

Catatan:
- Blok yang tampak berulang di hasil baca situs adalah **varian responsif Framer**. Di Laravel cukup satu komponen dengan CSS responsif.
- Judul dan meta description Home dan Product saat ini identik. Situs baru akan punya meta per halaman (tidak mengubah tampilan).

---

## 4. Spesifikasi 1:1 (diisi dari rekaman dan editor Framer)

> Status: **menunggu analisis rekaman**. Tabel di bawah adalah template yang harus terisi sebelum frontend dibangun.

### 4.1 Design token

| Token | Nilai |
|---|---|
| Warna (primer, sekunder, teks, latar, aksen) | Primer: `#693D22` (Rich Brown); Sekunder: `#FFE2CE` (Peach/Cream Icon Fill); Teks: `#333333` (Charcoal gelap), `#555555` (Muted), `#FFFFFF` (Putih); Latar: `#FFF8ED` (Warm Cream), `#FFFFFF`, `#693D22` (Why Choose section); Border: `#8888881A` / `#E5E7EB` |
| Font (family, weight, sumber/lisensi) | Serif (Headings): `Playfair Display`, serif (Google Fonts, 400, 600, 700); Sans (Body & UI): `Plus Jakarta Sans`, `Inter`, sans-serif (Google Fonts, 400, 500, 600) |
| Skala tipografi (h1 sampai body, per breakpoint) | H1 (Hero): 48-56px (Desktop), 36-40px (Tablet), 28-32px (Mobile); H2 (Section): 32-36px (Desktop), 26-30px (Tablet/Mobile); H3/Card: 18-20px; Body: 14-16px; Caption: 12-13px |
| Spacing / grid / lebar container | Container max: 1200px (`max-w-6xl` / `max-w-7xl`, `mx-auto px-4 sm:px-6 lg:px-8`); Section padding: `py-16 lg:py-24`; Grid produk: 4 kolom (desktop), 3/2 kolom (tablet), 1/2 kolom (mobile) |
| Radius, shadow, border | Tombol/Pill: `rounded-full` (40px); Kartu produk: `rounded-2xl` (16px); Input form: `rounded-xl` (10px); Modal: `rounded-3xl` (20px-24px); Shadow: `shadow-sm`, `shadow-lg` pada modal dan card hover |
| Breakpoint (desktop, tablet, mobile) | Desktop: `>= 1200px`; Tablet: `810px - 1199.98px`; Mobile: `< 810px` |

### 4.2 Inventaris interaksi

| Elemen | Trigger | Properti yang berubah | Durasi | Easing | Catatan |
|---|---|---|---|---|---|
| Navbar | Scroll | Background opacity, shadow, backdrop-filter | 200ms | ease-in-out | Sticky di atas layar |
| Hero (teks, gambar, tombol) | Load | Opacity 0->1, translateY 20px->0 | 600ms | ease-out | Staggered entrance |
| Furniture Set (tab, gambar, harga) | Click tab / hotspot | Active border, image focus, highlight info | 250ms | ease-in-out | Titik hotspot interaktif pada sofa, table, chair |
| Featured Products | Hover | translateY -4px, shadow-md | 200ms | ease-out | Kartu produk interaktif |
| Kartu produk (hover) | Hover | scale 1.02 / shadow | 200ms | ease-out | Gambar produk membesar halus |
| Tab kategori | Hover & Click | scale/color/border | 150ms | ease-in-out | Navigasi ke URL kategori masing-masing |
| Form kontak (fokus, kirim, sukses/gagal) | Focus / Submit | border-color, submit loading spinner, toast pesan | 200ms | ease-in-out | POST async / Alpine.js dengan alert |
| Menu mobile | Click hamburger | Drawer slide down / fade in | 300ms | cubic-bezier | Menu nav responsif mobile |
| Scroll / entrance animation | In-view | Opacity 0->1, translateY 30px->0 | 500ms | ease-out | Alpine intersect atau CSS reveal |

### 4.3 Perilaku khusus
- [x] Sticky navbar dan perubahan saat scroll
- [x] Smooth scroll anchor (`#contact`, `#sofa`, `#table`, `#arm-chair`)
- [x] Modal/lightbox video "Watch our Video" (YouTube ID: `8AHPXm9Y6mI`)
- [x] Modal "Download Catalog" (Form Name, Business Type, Email)
- [x] WhatsApp direct CTA (`https://wa.me/6282228764381?text=...`)

### 4.4 Konvensi folder `/screenshot`

Semua bahan visual ditaruh di folder `screenshot/` pada root proyek (di VS Code), supaya bisa dibaca langsung saat mengerjakan.

```
screenshot/
  home/
    desktop-01-hero.png
    desktop-02-furniture-set.png
    desktop-02-furniture-set-tab-table.png      (state tab lain)
    desktop-03-featured-products.png
    desktop-03-featured-products-hover.png      (state hover)
    mobile-01-hero.png
    mobile-menu-open.png
  product-indoor/
    desktop-01-header.png
    desktop-02-list.png
    mobile-01-list.png
  about-us/
  styles/
    colors.png
    typography.png
    spacing.png
  interactions.md       (catatan tertulis: elemen, trigger, durasi, easing)
```

Aturan penamaan: `{breakpoint}-{urutan}-{bagian}[-{state}].png`. Satu file per bagian dan per state (default, hover, aktif, terbuka). Tambahkan juga screenshot panel Styles dari editor Framer untuk token desain.

---

## 5. Arsitektur

### 5.1 Struktur folder

```
app/
  Http/Controllers/ (HomeController, CategoryController, PageController, InquiryController)
  Models/ (Category, Product, ProductImage, FurnitureSet, Inquiry, Setting)
  Services/ImageUploadService.php
  Filament/Resources/ (CategoryResource, ProductResource, FurnitureSetResource, InquiryResource)
  Filament/Pages/ (SiteSettings)
resources/
  views/
    layouts/app.blade.php
    components/ (navbar, footer, button, product-card, furniture-set, section-heading, contact-form)
    pages/ (home, category, about)
  css/app.css      (token Tailwind + animasi)
  js/app.js        (Alpine, interaksi)
```

Prinsip kode: satu komponen satu tanggung jawab, tanpa logika bisnis di Blade selain presentasi, nama class semantik (bukan hash), animasi dikumpulkan di satu tempat.

### 5.2 Routing

```php
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product-{category:slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/about-us', [PageController::class, 'about'])->name('about');
Route::post('/contact', [InquiryController::class, 'store'])
    ->middleware('throttle:5,1')->name('contact.store');
```

Slug kategori disimpan sebagai `indoor`, `outdoor-dining`, `pool-area`, `outdoor-living`, `rattan-poles`, `peacock-collection` agar URL identik dengan situs lama dan tidak perlu redirect.

---

## 6. Database

```php
// categories
Schema::create('categories', function (Blueprint $t) {
    $t->id();
    $t->string('name');
    $t->string('slug')->unique();
    $t->unsignedInteger('sort_order')->default(0);
    $t->boolean('is_active')->default(true);
    $t->string('meta_title')->nullable();
    $t->string('meta_description')->nullable();
    $t->timestamps();
});

// products
Schema::create('products', function (Blueprint $t) {
    $t->id();
    $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
    $t->string('code')->nullable();                 // FRP-RC-01
    $t->string('name');
    $t->string('slug')->unique();
    $t->string('material')->nullable();
    $t->text('description')->nullable();
    $t->unsignedSmallInteger('width_cm')->nullable();
    $t->unsignedSmallInteger('depth_cm')->nullable();
    $t->unsignedSmallInteger('height_cm')->nullable();
    $t->decimal('price_from', 10, 2)->nullable();   // USD, "Starts from $48"
    $t->boolean('is_active')->default(true);
    $t->boolean('is_featured')->default(false);
    $t->unsignedInteger('sort_order')->default(0);
    $t->softDeletes();
    $t->timestamps();
});

// product_images
Schema::create('product_images', function (Blueprint $t) {
    $t->id();
    $t->foreignId('product_id')->constrained()->cascadeOnDelete();
    $t->string('url', 2048);                        // URL publik Supabase
    $t->string('alt')->nullable();
    $t->unsignedInteger('sort_order')->default(0);
    $t->boolean('is_primary')->default(false);
    $t->timestamps();
});

// furniture_sets
Schema::create('furniture_sets', function (Blueprint $t) {
    $t->id();
    $t->string('name');
    $t->decimal('price', 10, 2)->nullable();        // harga normal (coret)
    $t->decimal('sale_price', 10, 2)->nullable();   // harga promo
    $t->string('image_url', 2048)->nullable();
    $t->text('whatsapp_message')->nullable();
    $t->unsignedInteger('sort_order')->default(0);
    $t->boolean('is_active')->default(true);
    $t->timestamps();
});

// furniture_set_product (pivot)
Schema::create('furniture_set_product', function (Blueprint $t) {
    $t->foreignId('furniture_set_id')->constrained()->cascadeOnDelete();
    $t->foreignId('product_id')->constrained()->cascadeOnDelete();
    $t->string('group')->nullable();                // sofa | table | arm-chair
    $t->unsignedInteger('sort_order')->default(0);
    $t->primary(['furniture_set_id', 'product_id']);
});

// inquiries (form kontak)
Schema::create('inquiries', function (Blueprint $t) {
    $t->id();
    $t->string('name');
    $t->string('email');
    $t->string('country')->nullable();
    $t->text('message');
    $t->string('ip', 45)->nullable();
    $t->timestamp('read_at')->nullable();
    $t->timestamps();
});

// settings (key-value): whatsapp_number, catalog_url, video_url,
// address, map_lat, map_lng, footer_tagline, copyright_text
```

Catatan:
- URL disimpan penuh sesuai keputusan. Bila project atau domain Supabase berganti, cukup satu query `REPLACE()` di kolom `url`.
- Path untuk hapus file diturunkan dari URL (`Str::after($url, $baseUrl)`).

---

## 7. Gambar: Supabase + webp

### 7.1 Konfigurasi

```bash
composer require league/flysystem-aws-s3-v3 "^3.0" intervention/image
```

```php
// config/filesystems.php -> 'disks'
'supabase' => [
    'driver' => 's3',
    'key' => env('SUPABASE_S3_KEY'),
    'secret' => env('SUPABASE_S3_SECRET'),
    'region' => env('SUPABASE_S3_REGION'),
    'bucket' => env('SUPABASE_BUCKET'),
    'endpoint' => env('SUPABASE_S3_ENDPOINT'),   // https://<project-ref>.storage.supabase.co/storage/v1/s3
    'use_path_style_endpoint' => true,
    'throw' => true,
],

// config/services.php
'supabase' => ['public_url' => env('SUPABASE_PUBLIC_URL')],
```

```env
SUPABASE_S3_KEY=
SUPABASE_S3_SECRET=
SUPABASE_S3_REGION=
SUPABASE_BUCKET=products
SUPABASE_S3_ENDPOINT=
SUPABASE_PUBLIC_URL=https://<project-ref>.supabase.co/storage/v1/object/public/products
```

Bucket dibuat **public**. Key S3 hanya ada di server.

### 7.2 Service

```php
<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageUploadService
{
    private const MAX_SIZE = 1600;
    private const QUALITY = 80;

    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /** Konversi ke webp, upload ke Supabase, kembalikan URL publik. */
    public function store(UploadedFile|string $file, string $folder = 'products'): string
    {
        $source = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $webp = $this->manager
            ->read($source)
            ->scaleDown(self::MAX_SIZE, self::MAX_SIZE)
            ->toWebp(self::QUALITY);

        $path = $folder . '/' . Str::uuid() . '.webp';

        Storage::disk('supabase')->put($path, (string) $webp, [
            'ContentType' => 'image/webp',
            'CacheControl' => 'public, max-age=31536000, immutable',
        ]);

        return $this->publicUrl($path);
    }

    public function delete(string $url): void
    {
        Storage::disk('supabase')->delete($this->pathFromUrl($url));
    }

    private function publicUrl(string $path): string
    {
        return rtrim(config('services.supabase.public_url'), '/') . '/' . $path;
    }

    private function pathFromUrl(string $url): string
    {
        return ltrim(Str::after($url, rtrim(config('services.supabase.public_url'), '/')), '/');
    }
}
```

### 7.3 Aturan gambar
- Ambil **file asli** dari editor Framer atau arsip, bukan dari CDN situs live (sudah di-resize).
- Nama file UUID + cache `immutable`: mengganti gambar otomatis mengganti URL.
- Semua `<img>` punya `width`, `height`, `alt`, dan `loading="lazy"` (kecuali gambar di atas lipatan) agar tidak ada layout shift.
- Cek `upload_max_filesize`, `post_max_size`, dan `memory_limit` hosting untuk foto besar.
- Pilih region Supabase terdekat dengan pengunjung utama.

---

## 8. CMS (Filament)

| Resource | Fitur |
|---|---|
| Category | CRUD, urutan, aktif/nonaktif, meta SEO |
| Product | CRUD: kode, nama, material, dimensi (W/D/H), harga mulai (USD), kategori, unggulan, aktif; banyak gambar dengan drag-urut dan penanda gambar utama |
| Furniture Set | CRUD: nama, harga, harga promo, gambar, pesan WhatsApp, pilih produk per grup (sofa/table/arm-chair) |
| Inquiry | Daftar pesan form kontak, tandai sudah dibaca |
| Site Settings | Nomor WhatsApp, file katalog, video, alamat dan koordinat peta, tagline, copyright |

Detail teknis:
- Field upload memakai `saveUploadedFileUsing` yang memanggil `ImageUploadService::store()`, sehingga state form berisi **URL**.
- Karena `FileUpload` Filament secara default mencari file di sebuah disk, preview gambar lama perlu `getUploadedFileUsing` yang mengembalikan URL. **Verifikasi sintaksnya ke dokumentasi versi Filament yang dipakai.**
- Hapus gambar atau produk memicu `ImageUploadService::delete()` lewat model event `deleting`.
- Hapus produk memakai soft delete.
- Satu role admin dulu. Jangan menambah sistem role bila belum perlu.

---

## 9. Form kontak

- Validasi: nama, email, negara, pesan.
- Simpan ke tabel `inquiries` + kirim email notifikasi ke alamat pemilik.
- Anti-spam: honeypot field + `throttle:5,1`; Cloudflare Turnstile bila spam masih lolos.
- Tampilan sukses/gagal harus meniru perilaku form saat ini (lihat bagian 4.2).
- Email notifikasi dikirim ke `nusarattanid@gmail.com` (simpan di `.env` atau Site Settings, jangan di-hardcode).

---

## 10. Pembersihan jejak Framer

Jalankan di akhir, hasil harus nol:

```bash
grep -rniE "framer|data-framer|framerusercontent|__framer" resources public app config
```

Checklist:
- [ ] `<meta name="generator">` dan dua tag `framer-search-index`
- [ ] Atribut `data-framer-*` dan class `framer-*`
- [ ] Script runtime Framer dan badge "Made in Framer" (jika ada)
- [ ] Semua gambar dari `framerusercontent.com` sudah dipindah ke Supabase
- [ ] Komentar, nama file, dan variabel yang mengandung "framer"
- [ ] Tidak ada hotlink ke CDN lain yang tidak disengaja
- [ ] Lisensi font dan aset sudah dipastikan boleh dipakai di luar Framer

---

## 11. Fase kerja dan estimasi

Total sekitar **10 sampai 16 hari kerja**. Variabel terbesar: jumlah dan kerumitan animasi.

### Fase 1 - Audit dan spesifikasi (1-2 hari)
- [x] Analisis rekaman layar (Home, 6 kategori, About Us; desktop dan mobile)
- [x] Isi tabel bagian 4 (token, interaksi, perilaku khusus)
- [x] Kumpulkan aset asli: foto, logo, SVG, font, PDF katalog, file video
- [x] Ekspor atau susun data produk per kategori (cek opsi ekspor CSV CMS Framer)
- [x] Keputusan di bagian 14 sudah terjawab

### Fase 2 - Fondasi (1 hari)
- [x] `laravel new`, Git, `.env`
- [x] Tailwind config dari design token
- [x] Struktur folder, layout, komponen dasar
- [x] Migration, model, seeder awal

### Fase 3 - Frontend (4-7 hari)
- [x] Layout, navbar, footer
- [x] Home: hero, Furniture Set, Why Choose, Featured Products, kontak
- [x] Halaman kategori (template tunggal, 6 URL)
- [x] About Us
- [x] Implementasi animasi sesuai tabel 4.2
- [x] Hubungkan ke data DB

### Fase 4 - CMS dan gambar (2-3 hari)
- [x] `ImageUploadService` + disk Supabase
- [x] Filament resources dan Site Settings
- [x] Import data produk dan gambar ke Supabase
- [x] Form kontak + notifikasi email

### Fase 5 - Uji 1:1 dan pembersihan (1-2 hari)
- [ ] Screenshot diff Playwright (bagian 12)
- [ ] Uji perilaku: hover, scroll, tab, menu mobile, form
- [ ] Pembersihan Framer (bagian 10)
- [ ] SEO: meta per halaman, canonical (www), sitemap, robots

### Fase 6 - Deploy dan cutover (1-2 hari)
- [ ] Deploy (bagian 13)
- [ ] Uji di perangkat nyata dan beberapa browser
- [ ] Cutover DNS, situs Framer tetap hidup sampai situs baru stabil

---

## 12. Uji 1:1 (terukur, bukan "kelihatannya sama")

- Playwright mengambil screenshot tiap halaman di tiga breakpoint, pada situs lama dan baru, dengan viewport dan kondisi animasi yang sama (tunggu animasi selesai atau nonaktifkan untuk uji statis).
- Bandingkan dengan pixelmatch. Selisih kecil akibat rendering font bisa ditoleransi, selisih tata letak tidak.
- Animasi diuji terpisah: rekam situs lama dan baru, bandingkan durasi dan easing frame demi frame.
- Setiap perbedaan dicatat di daftar, diperbaiki, lalu diuji ulang.

```js
// contoh kerangka (Playwright)
for (const bp of [1440, 820, 390]) {
  await page.setViewportSize({ width: bp, height: 900 });
  await page.goto(url, { waitUntil: 'networkidle' });
  await page.screenshot({ path: `shots/${name}-${bp}.png`, fullPage: true });
}
```

---

## 13. Deploy (hosting biasa)

- [ ] Document root mengarah ke `public/` (atau pindahkan isi `public/` ke `public_html` dan sesuaikan path di `index.php`)
- [ ] Build aset di lokal (`npm run build`), upload hasilnya
- [ ] `.env` produksi, `APP_DEBUG=false`, `APP_KEY` baru
- [ ] `php artisan migrate --force`, `config:cache`, `route:cache`, `view:cache`
- [ ] Cron untuk scheduler (bila dipakai) dan konfigurasi email SMTP
- [ ] Backup database otomatis
- [ ] Redirect `non-www` ke `www` (canonical saat ini `www.nusarattan.com`)
- [ ] Password admin kuat, batasi akses `/admin`

---

## 14. Keputusan (sudah terjawab)

| # | Topik | Keputusan | Dampak di implementasi |
|---|---|---|---|
| 1 | Meta per halaman | **Dibuat unik** | Kolom `meta_title` dan `meta_description` di kategori; fallback template untuk kategori dan About Us; Home memakai judul saat ini |
| 2 | Ejaan | **Diperbaiki** | "Coffee Tabel" menjadi "Coffee Table", "Lousiana" menjadi "Louisiana"; sisanya ditinjau saat import data (nama kolaborasi seperti "Bosanova" dikonfirmasi dulu) |
| 3 | Email form kontak | `nusarattanid@gmail.com` | Simpan di `.env` (`MAIL_TO_ADDRESS`) atau Site Settings |
| 4 | Pencarian | **Dipertahankan jika ada** | Cek di screenshot/rekaman; bila ada, bangun ulang dengan pencarian server-side atas tabel produk (query `LIKE` atau Laravel Scout driver database). Tag `framer-search-index` tetap dibuang |
| 5 | Video | **Embed YouTube** | Simpan ID/URL video di Site Settings; render iframe dengan perilaku yang sama (inline atau modal sesuai rekaman); pastikan URL embed tidak membawa parameter khas Framer |
| 6 | Halaman tersembunyi | **Tidak ada** | Lingkup halaman: Home, 6 kategori, About Us |

Draf meta (diperiksa ulang saat Fase 5):

- Home: tetap seperti sekarang.
- Kategori: `{Nama Kategori} | Rattan Furniture by Nusarattan` dengan deskripsi `Browse {kategori} from Nusarattan, Indonesian rattan furniture manufacturer. Custom designs, wholesale export, handcrafted by local artisans.`
- About Us: `About Nusarattan | Indonesian Rattan Furniture Manufacturer`

---

## 15. Risiko

| Risiko | Mitigasi |
|---|---|
| Animasi tidak persis sama | Ukur durasi dan easing dari rekaman, uji frame demi frame |
| Font atau aset berlisensi Framer | Periksa lisensi sebelum dipakai di luar Framer |
| Gambar asli tidak tersedia | Minta dari arsip/editor; bila tidak ada, kualitas terbatas pada versi CDN |
| Batasan hosting (memory, upload size) | Uji upload gambar besar lebih awal, kecilkan batas dimensi bila perlu |
| Ketergantungan pada Supabase | URL disimpan di satu kolom, pindah bucket cukup dengan `REPLACE()` |
| Bahan audit kurang (interaksi tidak terekam) | Rekam ulang bagian yang kurang sebelum Fase 3 |
