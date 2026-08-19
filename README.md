# Warext Studios - Konu Güncellik Sistemi

XenForo 2.3 için topluluk tabanlı konu ve çözüm güncellik doğrulama eklentisi.

## Sürüm

**1.0.0 Stable**

## Ana özellikler

- Forum bazlı etkinleştirme ve bekleme süresi
- Çalıştı / çalışmadı topluluk oylaması
- Tek kullanıcı / tek oy ve oy değiştirme izni
- Hesap yaşı ve mesaj sayısı koşulları
- Sürüm bazlı doğrulama sonuçları
- Güncel, muhtemelen güncel, kararsız, şüpheli, çalışmıyor, yeniden doğrulanıyor ve doğrulanmamış durumları
- Eski oyları zamanla düşük ağırlıkla değerlendirme
- Otomatik yeniden doğrulama
- Konu listesi rozetleri ve durum filtreleri
- Moderatör doğrulaması
- Kritik durum değişikliği bildirimleri ve web push
- Güncel çözüm konusu yönlendirmesi
- Güncel Çözümler arama sayfası
- ACP içerik sağlığı ve kritik konu özeti
- Eşzamanlı oy kilitleme
- Orphan kayıt temizliği
- PHP 8.4 otomatik testleri
- Kurulabilir `_data` XML içeren release paketi

## Gereksinimler

- XenForo 2.3.0+
- PHP 8.1+

## Kurulum

GitHub Actions tarafından üretilen `Warext-Studios-Konu-Guncellik-Sistemi-1.0.0.zip` paketini XenForo yönetim panelindeki arşivden eklenti kurma ekranından yükleyin.

Kurulumdan sonra:

1. Eklenti seçeneklerinden sistemi etkinleştirin.
2. İlgili forumun düzenleme ekranından konu güncellik doğrulamasını açın.
3. Forum için bekleme süresini ve gerekiyorsa sürüm listesini ayarlayın.
4. Kullanıcı grubu izinlerinden oy, oy değiştirme, kendi konusuna oy ve moderatör doğrulama izinlerini yapılandırın.

## Veri tabanı

Manuel SQL içe aktarma gerekmez. Kurulum ve yükseltme işlemleri `Setup.php` üzerinden yürütülür.
