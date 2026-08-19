# Değişiklik Günlüğü

## 1.0.0

- Stable sürüm tamamlandı.
- Güncel çözüm konusu yönlendirmesi eklendi.
- Güncel Çözümler arama sayfası eklendi.
- ACP içerik sağlığı özeti ve kritik konu listesi eklendi.
- Alpha 6'dan stable sürüme güvenli şema yükseltmesi eklendi.
- Replacement thread kayıtlarının orphan temizliği eklendi.
- `_data` XML üretim aracı eklendi.
- Release paket hazırlama ve SHA-256 hashes.json üretimi eklendi.
- PHP kaynaklarında comment/TODO bulunmamasını doğrulayan güvenlik testi eklendi.
- Mutating endpoint POST koruması, eşzamanlılık kilidi ve temel şema indeksleri otomatik kontrole bağlandı.
- GitHub Actions üzerinden kurulabilir 1.0.0 ZIP üretimi eklendi.

## 1.0.0 Alpha 6

- Konu sahipleri için anlamlı durum değişikliği bildirimleri eklendi.
- XenForo web push bildirim şablonu eklendi.
- Bildirimler yalnızca kritik veya iyileşen durum geçişleriyle sınırlandırıldı.
- Bildirimleri ACP üzerinden açıp kapatma seçeneği eklendi.
- Oy ve moderatör işlemleri konu bazlı veritabanı kilidine bağlandı.
- Rebuild job işlemleri güvenli yeniden hesaplama akışına taşındı.
- Orphan oy, durum ve geçmiş kayıtlarını temizleyen günlük cron eklendi.
- Cleanup sorguları MySQL/MariaDB uyumlu sınırlı ID seçimi ve ardından silme akışına geçirildi.
- Bildirim durum testleri eklendi.

## 1.0.0 Alpha 5

- Konu listesi güncellik rozetleri eklendi.
- Forum konu listeleri için durum kayıtlarının toplu ön yüklemesi eklendi.
- Durum filtresi backend'i eklendi.
- Forum görünümüne güncellik filtre menüsü eklendi.
- Oy değiştirme izni ayrı permission haline getirildi.
- Kendi konusuna oy verme izni ayrı permission haline getirildi.
- Moderatör doğrulama izni ve endpoint'i eklendi.
- Moderatör doğrulama servisi ve durum override mantığı eklendi.
- Moderatör durum geçişleri geçmiş kaydına bağlandı.
- Oy servisinde permission kontrolleri ikinci kez doğrulanacak şekilde sertleştirildi.
- Moderatör durum testleri eklendi.

## 1.0.0 Alpha 4

- Forum bazlı etkinleştirme doğrudan forum düzenleme ekranına taşındı.
- Forum başına bekleme süresi ve sürüm listesi eklendi.
- Sürüm bazlı ayrı sonuç hesaplaması eklendi.
- Yeniden doğrulama döngüsü eklendi.
- Günlük yeniden hesaplama cron'u eklendi.
- Eski forum ID ayarları için migration eklendi.

## 1.0.0 Alpha 3

- Konu sayfası doğrulama paneli eklendi.
- Oy endpoint'i eklendi.
- Oy permission kontrolü eklendi.
- Kullanıcı uygunluk kontrolleri eklendi.
- Olumsuz oy nedenleri ve sürüm alanı eklendi.

## 1.0.0 Alpha 2

- XenForo 2.3 uyumluluk düzeltmeleri yapıldı.
- Oy güncelleme zamanının ağırlık hesabına etkisi düzeltildi.
- Rebuild job dönüş tipi düzeltildi.
- Otomatik PHP 8.4 ve algoritma testleri eklendi.

## 1.0.0 Alpha 1

- İlk eklenti iskeleti oluşturuldu.
- Veritabanı tabloları eklendi.
- Entity, Repository, Service ve Job katmanları eklendi.
- Topluluk oy ağırlıklandırması ve durum hesaplama motoru eklendi.
