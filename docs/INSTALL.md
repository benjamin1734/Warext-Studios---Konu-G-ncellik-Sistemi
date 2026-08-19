# Kurulum ve Yükseltme

## Gereksinimler

- XenForo 2.3.0 veya üzeri
- PHP 8.1 veya üzeri

## Yeni kurulum

1. `Warext-Studios-Konu-Guncellik-Sistemi-1.1.0.zip` dosyasını XenForo ACP üzerinden arşivden eklenti kurma ekranına yükleyin.
2. `Warext Studios - Konu Güncellik Sistemi` eklentisini kurun.
3. Eklenti seçeneklerinden sistemi etkinleştirin ve durum eşiklerini ihtiyacınıza göre ayarlayın.
4. Kullanıcı grubu izinlerinden oy verme, oy değiştirme, kendi konusuna oy ve moderatör doğrulama yetkilerini düzenleyin.
5. Doğrulama kullanacak forumların düzenleme ekranında sistemi açın, bekleme gününü ve yaş hesabı yöntemini seçin.

## 1.0.0 → 1.1.0

1. 1.1.0 ZIP paketini ACP üzerinden yükseltme olarak yükleyin.
2. Manuel SQL çalıştırmayın. Yeni kolonlar ve indeksler `Setup.php` yükseltme adımları tarafından eklenir.
3. Yükseltme sonunda state kayıtları yeniden hesaplama job'una alınır.
4. Forum ayarlarında varsayılan yaş hesabı `Anlamlı güncelleme` olarak kullanılır; istenirse forum bazında `Her son mesaj` seçilebilir.

## Kaldırma

Standart XenForo eklenti kaldırma akışını kullanın. Eklentiye ait tablolar ve forum kolonları `Setup.php` tarafından temizlenir.
