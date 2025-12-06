# LevFrame

Bu proje, temel Laravel bileşenlerini kullanan hafif ve özelleştirilebilir bir PHP uygulamasıdır.

## Kurulum

Projeyi Composer kullanarak aşağıdaki komutla kurabilirsiniz:

```bash
composer create-project leventust/levframe myapp
```

## Klasör Yapısı

Projenin temel dizin yapısı aşağıdaki gibidir:

```
.
├── console             # Konsol komutlarını çalıştıran dosya
├── public/             # Web sunucusu kök dizini (index.php buradadır)
├── config/             # Ayar dosyaları
├── database/           # Veritabanı ve migrasyon dosyaları
└── src/
    ├── app/            # Uygulama kodları
    │   ├── Controllers/  # Kontrolcüler (Controller)
    │   ├── Jobs/         # İş kuyruğu sınıfları (Job)
    │   ├── Models/       # Veritabanı modelleri (Model)
    │   └── routes.php    # Rota tanımları
    └── core/           # Çekirdek dosyalar (Framework core)
```

## Konsol Komutları

Proje içerisindeki işlemleri `php console` komutu ile gerçekleştirebilirsiniz.

### İş Kuyruğu (Queue/Job) Komutları

| Komut | Açıklama |
|-------|----------|
| `php console make:job <Isim>` | Yeni bir Job (İş) sınıfı oluşturur. |
| `php console queue:work` | Kuyruktaki işleri işlemeye başlar. |
| `php console make:queue-table` | Kuyruk tablosu için migrasyon oluşturur. |
| `php console make:queue-failed-table` | Hatalı işler tablosu için migrasyon oluşturur. |
| `php console queue:failed` | Hatalı işleri listeler. |
| `php console queue:retry <id>` | Hatalı bir işi tekrar kuyruğa ekler. |

### Veritabanı & Migrasyon Komutları

| Komut | Açıklama |
|-------|----------|
| `php console make:migration <Isim>` | Yeni bir veritabanı migrasyonu oluşturur. |
| `php console migrate` | Migrasyonları çalıştırır (Tabloları oluşturur). |
| `php console migrate:rollback` | Son yapılan migrasyonu geri alır. |
| `php console migrate:status` | Migrasyonların durumunu gösterir. |

## Nasıl Kullanılır?

### 1. Kurulum ve Başlangıç
Projeyi oluşturduktan sonra dizine girin ve gerekli ayarları yapın:
```bash
cd myapp
cp .env.example .env
# .env dosyasındaki veritabanı ayarlarını düzenleyin
```

### 2. Veritabanı Migrasyonları

#### Yeni Bir Tablo Oluşturma
Yeni bir tablo oluşturmak için `--create` parametresini kullanın:
```bash
php console make:migration create_products_table --create=products
```
Oluşan dosya `database/migrations` klasöründe olacaktır. İçeriğini şu şekilde düzenleyebilirsiniz:
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 8, 2);
    $table->timestamps();
});
```

#### Mevcut Tabloyu Güncelleme
Var olan bir tabloya sütun eklemek veya değiştirmek için `--table` parametresini kullanın:
```bash
php console make:migration add_stock_to_products_table --table=products
```
Dosya içeriği:
```php
Schema::table('products', function (Blueprint $table) {
    $table->integer('stock')->default(0);
});
```

Migrasyonları çalıştırmak için:
```bash
php console migrate
```

### 3. Yeni Bir Job Oluşturma
Konsoldan aşağıdaki komutu çalıştırın:
```bash
php console make:job OrnekIslemJob
```
Bu komut `src/app/Jobs/OrnekIslemJob.php` dosyasını oluşturacaktır.

### 4. İşleri Çalıştırma (Worker)
Kuyruğa atılan işlerin arka planda işlenmesi için worker'ı başlatın:
```bash
php console queue:work
```
