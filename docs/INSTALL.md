# Kurulum ve Yükseltme

## Gereksinimler

- XenForo 2.3.0 veya üzeri
- PHP 8.1 veya üzeri

## Yeni kurulum

1. `Warext-Studios-Konu-Guncellik-Sistemi-1.0.0.zip` dosyasını XenForo ACP üzerinden arşivden eklenti kurma ekranına yükleyin.
2. `Warext Studios - Konu Güncellik Sistemi` eklentisini kurun.
3. Eklenti seçeneklerinden sistemi etkinleştirin ve durum eşiklerini ihtiyacınıza göre ayarlayın.
4. Kullanıcı grubu izinlerinden oy verme, oy değiştirme, kendi konusuna oy ve moderatör doğrulama yetkilerini düzenleyin.
5. Doğrulama kullanacak forumların düzenleme ekranında sistemi açın, bekleme gününü ve yaş hesabı yöntemini seçin.

## Yükseltme

- Manuel SQL çalıştırmayın. Gerekli kolon ve indeks işlemleri `Setup.php` üzerinden yürütülür.
- Eski geliştirme/alpha yapılarından gelen kurulumlarda gerekli migration adımları otomatik çalışır.
- Yeniden hesaplama gerektiren durumlarda eklenti ilgili job'u otomatik olarak kuyruğa alır.
- Forum ayarlarında önerilen yaş hesabı `Anlamlı güncelleme` modudur; gerekirse forum bazında `Her son mesaj` seçilebilir.

## Kaldırma

Standart XenForo eklenti kaldırma akışını kullanın. Eklentiye ait tablolar ve forum kolonları `Setup.php` tarafından temizlenir.
