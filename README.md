# SYS Panel - Süreç Yönetim Sistemi

Bu proje, personel izinleri, görev takibi, duyurular, mesajlaşma ve dosya paylaşımı gibi süreçleri yönetmek için geliştirilmiş kapsamlı bir web uygulamasıdır. Laravel framework'ü kullanılarak geliştirilmiştir.

## Özellikler

*   **İzin Yönetimi:** Personel izin talepleri oluşturabilir, yöneticiler onaylayabilir veya reddedebilir.
*   **Görev Takibi:** Görev atama, durum güncelleme (Beklemede, Devam Ediyor, Tamamlandı) ve takip.
*   **İş Takvimi:** Etkinliklerin ve görevlerin takvim üzerinde görüntülenmesi.
*   **Duyurular:** Yöneticiler tarafından tüm personele duyuru yayınlama.
*   **Mesajlaşma:** Kullanıcılar arası birebir mesajlaşma ve genel sohbet odası.
*   **Dosya Paylaşımı:** Dosya yükleme ve indirme.
*   **Anketler:** Yönetici tarafından oluşturulan anketlere personelin katılımı.
*   **Departman Yönetimi:** Departman oluşturma ve düzenleme.
*   **Kullanıcı Yönetimi:** Kullanıcı ekleme, düzenleme ve rol (Yönetici/Personel) atama.
*   **Platform Ayarları:** Site başlığı, logo ve favicon yönetimi.
*   **Dinamik Arayüz:** Kullanıcı dostu ve responsive tasarım (Fillow teması).

## Gereksinimler

*   PHP 8.1 veya üzeri
*   Composer
*   Node.js ve NPM
*   MySQL

## Kurulum

Projeyi yerel ortamınızda çalıştırmak için aşağıdaki adımları izleyin:

1.  **Projeyi İndirin:**
    ```bash
    git clone [repo-url]
    cd sys-panel
    ```

2.  **PHP Bağımlılıklarını Yükleyin:**
    ```bash
    composer install
    ```

3.  **Frontend Bağımlılıklarını Yükleyin ve Derleyin:**
    ```bash
    npm install
    npm run build
    ```

4.  **Çevre Değişkenlerini Ayarlayın:**
    `.env.example` dosyasını kopyalayarak `.env` dosyasını oluşturun ve veritabanı ayarlarınızı yapın.
    ```bash
    cp .env.example .env
    ```
    `.env` dosyasını açın ve veritabanı bilgilerinizi girin:
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=izin_db
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5.  **Uygulama Anahtarını Oluşturun:**
    ```bash
    php artisan key:generate
    ```

6.  **Veritabanı Tablolarını Oluşturun:**
    ```bash
    php artisan migrate
    ```

7.  **Dosya Bağlantısını Oluşturun:**
    Resimlerin ve dosyaların düzgün görüntülenmesi için storage bağlantısını oluşturun.
    ```bash
    php artisan storage:link
    ```

8.  **Uygulamayı Başlatın:**
    ```bash
    php artisan serve
    ```
    Uygulama `http://localhost:8000` adresinde çalışacaktır.

## Kullanım

*   **Giriş Yap:** Yöneticiniz tarafından size verilen bilgilerle sisteme giriş yapabilirsiniz.
*   **Kullanıcı Ekleme:** Sisteme dışarıdan kayıt olunamaz. Yeni kullanıcılar sadece **Yönetici** yetkisine sahip kullanıcılar tarafından "Kullanıcı Yönetimi" sayfasından eklenebilir.
*   **Yönetici Hesabı:** İlk kurulumda veritabanında manuel olarak bir kullanıcı oluşturulması veya seeder kullanılması gerekebilir. Mevcut bir kullanıcıyı yönetici yapmak için:
    ```bash
    php artisan tinker
    $user = App\Models\User::first();
    $user->is_admin = true;
    $user->save();
    exit
    ```

## Kalite ve Güvenlik Kontrolleri

Lokal kalite kontrolleri:

```bash
composer run lint
composer run analyse
composer run test
composer run quality
```

Test ortamı için `.env.testing` dosyası hazırdır. CI tarafında:

* `.github/workflows/quality.yml` ile `Pint + PHPStan + PHPUnit`
* `.github/workflows/secret-scan.yml` ile secret taraması

Operasyonel secret yönetimi ve rotasyon adımları:

* `docs/security-operations.md`

## Katkıda Bulunma

1.  Bu depoyu forklayın.
2.  Yeni bir özellik dalı oluşturun (`git checkout -b yeni-ozellik`).
3.  Değişikliklerinizi commit edin (`git commit -am 'Yeni özellik eklendi'`).
4.  Dalınızı pushlayın (`git push origin yeni-ozellik`).
5.  Bir Pull Request oluşturun.
