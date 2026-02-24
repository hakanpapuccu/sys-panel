# Security Operations Guide

Bu doküman üretim ortamı için operasyonel güvenlik adımlarını içerir.

## 1) Secret Rotation (Zorunlu)

Depoda daha önce `.env` bulunduğu için aşağıdaki sırayla **tüm kritik sırları döndürün**:

1. `APP_KEY`
2. Veritabanı kullanıcı parolaları
3. SMTP kullanıcı/parolaları
4. Zoom/Teams istemci secret değerleri
5. Varsa API token/JWT signing secret değerleri

Not: `APP_KEY` döndürme işlemi mevcut şifreli verileri, oturumları ve remember token davranışını etkileyebilir. Operasyon öncesi bakım penceresi planlayın.

## 2) Git History Temizliği (Gerekirse)

Geçmiş commit’lerde sızan secret varsa sadece `.gitignore` güncellemesi yeterli değildir. Geçmişi temizlemek için:

1. Secret’ları önce döndürün.
2. Geçmişi `git filter-repo` veya BFG ile temizleyin.
3. Zorunlu push (`--force-with-lease`) sonrası tüm ekip üyeleri depoyu temiz klonlasın.

## 3) Üretim Güvenlik Ayarları

Üretimde aşağıdaki env değerlerini kullanın:

- `APP_DEBUG=false`
- `FORCE_HTTPS=true`
- `SESSION_SECURE_COOKIE=true`
- `SECURITY_HSTS_ENABLED=true`
- `SECURITY_HSTS_INCLUDE_SUBDOMAINS=true` (ihtiyaca göre)

## 4) Sürekli Kontrol

- CI üzerinde `Secret Scan` workflow’unu zorunlu tutun.
- Tüm PR’lar için `Quality Gate` (Pint + PHPStan + PHPUnit) geçişini zorunlu tutun.
- En az 90 günde bir secret rotation planlayın.
