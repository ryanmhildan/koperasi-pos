# Project: Koperasi Terpadu - Laravel 11 + Livewire + Spatie + Tailwind

Dokumen ini adalah *scaffolding plan* dan artefak awal (perintah, file contoh, migration & seeder sample, serta instruksi) untuk membangun aplikasi **Koperasi Terpadu** sesuai ERD, flowchart, dan preferensi kamu.

> Pilihan yang kamu konfirmasi:
>
> * Environment: **lokal**
> * Frontend build: **Vite**
> * Auth scaffold: **Laravel Breeze (Livewire stack)**
> * Authorization: **Spatie - Role + Permission**
> * API: **API key / token**
> * Harga rata-rata: **Per location**, update **realtime on GRN**
> * UI: **Tailwind** (Bahasa Indonesia)
> * Storage: **Local storage**
> * Cetak struk thermal: **Ya**
> * Deliverables: `Repo Git`, `Docker Compose + .env.example`, `README`, `Seeder contoh`

---

## 1. Struktur repo & langkah awal (quick start)

**Prereq (lokal)**

* PHP >= 8.2
* Composer
* Node.js >= 18 + npm/yarn
* MySQL (boleh belum dibuat DB — nanti migrasi)
* Git

**Buat project Laravel 11**

```bash
composer create-project laravel/laravel koperasi-web "^11.0" --prefer-dist
cd koperasi-web
```

**Install Breeze (Livewire stack)**

```bash
composer require laravel/breeze --dev
php artisan breeze:install livewire
npm install
npm run dev
```

**Install Livewire (jika belum)**

```bash
composer require livewire/livewire
```

**Install Spatie Permission**

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Kemudian tambahkan trait pada model `User`:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    // ...
}
```

**Instalasi Tailwind & Vite** (Laravel Breeze akan menyiapkan Vite)

```bash
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

Tambahkan konfigurasi Tailwind di `tailwind.config.js` dan import di `resources/css/app.css` sesuai standar Breeze.

---

## 2. Composer & NPM packages tambahan yang direkomendasikan

```text
composer require spatie/laravel-permission
composer require laravel/sanctum        # untuk API token / key
composer require barryvdh/laravel-dompdf # optional untuk PDF (laporan/struk)
```

NPM:

```bash
npm install @fontsource/inter  # contoh font
```

---

## 3. Docker Compose (opsional) & .env.example

Walau kamu pilih environment lokal, kamu minta Docker Compose contoh — berikut `docker-compose.yml` minimal (opsional, pakai jika mau konsisten nanti):

```yaml
version: '3.8'
services:
  app:
    image: php:8.2-fpm
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
    depends_on:
      - db
  node:
    image: node:20
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: koperasi
      MYSQL_USER: user
      MYSQL_PASSWORD: secret
    ports:
      - "3306:3306"
    volumes:
      - dbdata:/var/lib/mysql
volumes:
  dbdata:
```

Contoh `.env.example` (sesuaikan nanti):

```
APP_NAME=Koperasi
APP_ENV=local
APP_KEY=base64:GENERATE_LATER
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=koperasi
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=
```

---

## 4. Migration: daftar migration awal (sesuai ERD + penyesuaian)

Berikut file migration sample (ringkasan). Simpan di `database/migrations/`.

### users (default Breeze) — tambahkan kolom yang diperlukan

```php
$table->id('user_id');
$table->string('nrp')->unique()->nullable();
$table->string('username')->unique();
$table->string('password');
$table->string('email')->unique();
$table->string('full_name')->nullable();
$table->string('phone')->nullable();
$table->date('join_date')->nullable();
$table->boolean('is_active')->default(true);
$table->timestamps();
```

### roles & permissions

* Dihandle oleh Spatie (vendor publish -> migrations otomatis)

### user\_roles (opsional karena Spatie menyimpan relasi)

* Kamu tidak wajib membuat USER\_ROLES manual ketika memakai Spatie. Spatie menyimpan pada tabel `model_has_roles`.

### user\_credit\_cards

```php
$table->id('card_id');
$table->foreignId('user_id')->constrained('users','user_id')->cascadeOnDelete();
$table->string('card_number');
$table->decimal('credit_limit', 15, 2)->default(0);
$table->decimal('current_balance', 15, 2)->default(0);
$table->decimal('cash_out_limit', 15, 2)->default(0);
$table->decimal('cash_out_used_this_month', 15, 2)->default(0);
$table->string('expiry_date')->nullable();
$table->string('bank_name')->nullable();
$table->boolean('is_active')->default(true);
$table->timestamps();
```

### simpanan

```php
$table->id('simpanan_id');
$table->foreignId('user_id')->constrained('users','user_id');
$table->decimal('amount', 15, 2);
$table->date('transaction_date');
$table->string('description')->nullable();
$table->timestamps();
```

### pinjaman

```php
$table->id('pinjaman_id');
$table->foreignId('user_id')->constrained('users','user_id');
$table->decimal('loan_amount', 15, 2);
$table->decimal('interest_rate', 8, 2);
$table->integer('tenor_months');
$table->string('loan_type')->nullable();
$table->string('loan_purpose')->nullable();
$table->date('loan_date')->nullable();
$table->enum('status', ['active','closed'])->default('active');
$table->boolean('is_blocked')->default(false);
$table->decimal('total_paid', 15, 2)->default(0);
$table->decimal('remaining_balance', 15, 2)->default(0);
$table->timestamps();
```

### angsuran

```php
$table->id('angsuran_id');
$table->foreignId('pinjaman_id')->constrained('pinjaman','pinjaman_id');
$table->decimal('amount', 15, 2);
$table->date('due_date');
$table->date('paid_date')->nullable();
$table->enum('status',['pending','paid','late'])->default('pending');
$table->decimal('denda', 15, 2)->default(0);
$table->timestamps();
```

### locations, units, categories, products

(sesuai ERD, gunakan foreign keys)

### prices (penting: tambahkan location\_id)

```php
$table->id('price_id');
$table->foreignId('product_id')->constrained('products','product_id');
$table->foreignId('location_id')->constrained('locations','location_id');
$table->decimal('average_price', 15, 2)->default(0);
$table->integer('total_stock')->default(0);
$table->decimal('total_value', 18, 2)->default(0);
$table->timestamps();
```

### stock, stock\_movements, good\_receipt\_notes (GRN)

* `good_receipt_notes` harus menyimpan `supplier_id` (opsional), `unit_cost`, `quantity`, `location_id`, `product_id`, `receipt_date`, `reference_number`.
* Ketika menyimpan GRN, lakukan transaksi DB: update `stock` (+quantity pada location), insert `stock_movements` (movement\_type=in), dan recalculate `prices` untuk `product_id+location_id` (realtime on GRN).

---

## 5. Contoh fungsi: Kalkulasi Harga Rata-Rata (Realtime on GRN)

Contoh langkah di controller / service ketika GRN disimpan:

```php
DB::transaction(function() use ($productId, $locationId, $qty, $unitCost) {
    // 1. simpan GRN (good_receipt_notes)
    GoodReceipt::create([...]);

    // 2. insert stock_movements
    StockMovement::create([... 'movement_type' => 'in']);

    // 3. update stock table (increment)
    $stock = Stock::firstOrNew(['product_id' => $productId, 'location_id' => $locationId]);
    $stock->current_stock += $qty;
    $stock->save();

    // 4. recalc prices (weighted average)
    $price = Price::firstOrNew(['product_id' => $productId, 'location_id' => $locationId]);
    $existingTotalValue = $price->total_value; // total_value = avg * total_stock
    $existingTotalStock = $price->total_stock;

    $newTotalValue = $existingTotalValue + ($unitCost * $qty);
    $newTotalStock = $existingTotalStock + $qty;
    $price->average_price = $newTotalValue / max(1, $newTotalStock);
    $price->total_stock = $newTotalStock;
    $price->total_value = $newTotalValue;
    $price->save();
});
```

---

## 6. Livewire components (awal) — saran struktur

```
app/Http/Livewire/
  - Auth/
  - Admin/
    - UsersTable.php
    - ProductsCrud.php
    - LocationsCrud.php
    - GRNCreate.php
  - POS/
    - ShiftOpen.php
    - POSRegister.php
    - ReceiptPrint.php
  - Finance/
    - SimpananCreate.php
    - PinjamanCreate.php
    - AngsuranPay.php
```

Setiap component punya view di `resources/views/livewire/...`.

---

## 7. API: API Key / Token (Laravel Sanctum)

* Gunakan `laravel/sanctum` untuk issuing token. Buat middleware check `sanctum` dan policy untuk endpoint POS atau API kunci.

Contoh endpoint menghasilkan API key untuk user:

```php
Route::post('/api/generate-key', function(Request $r) {
  $user = Auth::user();
  $token = $user->createToken('api-key-for-pos');
  return ['token' => $token->plainTextToken];
});
```

Untuk penggunaan API key sebagai autentikasi di POS offline, gunakan header `Authorization: Bearer {token}`.

---

## 8. Thermal Printing (struk)

Pilihan implementasi:

1. **Cetak via browser (window\.print)** — paling mudah, cetak layout struk minimal. (supported by POS printers via browser print)
2. **ESC/POS via server** — pakai paket PHP/JS untuk kirim perintah ESC/POS ke printer jaringan. (contoh: `mike42/escpos-php`)
3. **WebSocket / Bridge App** — gunakan aplikasi kecil yang menjalankan print job lokal (lebih reliable untuk printer USB).

Mulai dengan: **layout cetak HTML** + `window.print()`; nanti tambah integrasi ESC/POS bila butuh.

---

## 9. Seeder contoh (produk, user, lokasi)

Contoh seeder singkat (simpan di `database/seeders/DatabaseSeeder.php`):

```php
public function run()
{
    // Roles & Permissions
    $adminRole = Spatie\Permission\Models\Role::create(['name' => 'admin']);
    $kasirRole = Spatie\Permission\Models\Role::create(['name' => 'kasir']);
    $anggotaRole = Spatie\Permission\Models\Role::create(['name' => 'anggota']);

    // Users
    $admin = User::factory()->create([ 'username' => 'admin', 'email' => 'admin@local' ]);
    $admin->assignRole('admin');

    // Locations
    Location::create(['location_name' => 'Cabang Ciroyom', 'address' => 'Jl. Ciroyom', 'is_active' => true]);
    Location::create(['location_name' => 'Cabang Kosambi', 'address' => 'Jl. Kosambi', 'is_active' => true]);

    // Product contoh
    $p = Product::create(['product_code'=>'TISU001','product_name'=>'Tisu Gulung','category_id'=>1,'unit_id'=>1,'selling_price'=>12000,'is_stock_item'=>1,'product_type'=>'retail','minimum_stock'=>5,'is_active'=>1]);
}
```

---

## 10. Routes & RBAC

* Gunakan middleware `auth` + `role:admin` / `role:kasir` pada route group. Contoh:

```php
Route::middleware(['auth'])->group(function() {
  Route::get('/pos', POSController::class)->middleware('role:kasir|admin');
  Route::resource('products', ProductController::class)->middleware('role:admin');
});
```

---

## 11. Checklist deliverables yang akan kubuat sekarang

* [x] README & instruksi (ini)
* [ ] Struktur repo (composer + vite + tailwind + breeze scaffolding) — **instruksi + file template**
* [ ] Migration skeleton (file contoh di `database/migrations/`) — **sampel snippet di atas**
* [ ] Seeder contoh (produk, user, lokasi) — snippet di atas
* [ ] Livewire components list & contoh
* [ ] Docker Compose minimal + `.env.example`

> Aku akan mulai generate file-file skeleton & kode di repo jika kamu setuju. Karena kamu bekerja di lingkungan lokal dan minta repo Git, aku akan sertakan instruksi commit & push ke GitHub.

---

## 12. Langkah selanjutnya (apa yang akan aku kerjakan sekarang)

1. Buat skeleton repo (struktur folder, composer.json) & contoh file konfigurasi (`tailwind.config.js`, `vite.config.js`), Breeze & Spatie setup steps (otomatis di README).
2. Siapkan migration files utama (users, products, locations, prices, stock, good\_receipt\_notes, stock\_movements, sales\_transactions, sales\_transaction\_details, cash\_drawers, simpanan, pinjaman, angsuran, user\_credit\_cards).
3. Buat seeder contoh (users, roles, locations, products).
4. Buat contoh Livewire component `GRNCreate` dan `POSRegister` skeleton dengan kode perhitungan harga rata-rata.

Kalau kamu setuju, ketik **"lanjut buat repo"** dan aku akan mulai membuat file-file scaffold (aku akan menaruh semua file contoh di repo yang nanti bisa kamu clone). Jika ada tambahan permintaan khusus (misal: nama repo GitHub), sebutkan sekarang; kalau tidak, aku mulai dengan repo bernama `koperasi-web`.
