# Değişiklik Günlüğü

## 1.1.0

- Konu yaş hesabına forum bazlı `Anlamlı güncelleme` ve `Her son mesaj` modları eklendi.
- Anlamlı güncelleme tarihi konu sahibinin mesajları ve ilk mesaj düzenlemeleri üzerinden hesaplanır hale getirildi.
- Yeni anlamlı güncellemeden önceki oylar yeni doğrulama döngüsünde hesaba katılmayacak şekilde düzeltildi.
- Eski döngüde oy kullanmış kullanıcıların yeni doğrulama döngüsünde tekrar oy kullanabilmesi sağlandı.
- Durum minimum eşikleri ham oy sayısına, yüzde hesabı zaman ağırlıklı oy skoruna bağlandı.
- Durum hesaplama eşikleri ACP üzerinden değiştirilebilir hale getirildi.
- Forum filtre şablonundaki hatalı string `replace` kullanımı `array_merge` ile düzeltildi.
- Forum filtreleri moderatör override durumunu dikkate alacak şekilde düzeltildi.
- State kaydı olmayan doğrulanmamış konular Güncel Çözümler aramasına dahil edildi.
- Arama taraması izin ve dinamik güncellik kontrolleri için parçalı taramaya geçirildi.
- Kendi konusuna oy verme ACP anahtarı gerçek izin kontrolüne bağlandı.
- Olumsuz oy nedenleri backend tarafında whitelist doğrulamasına bağlandı.
- Olumsuz oylara isteğe bağlı daha güncel konu önerisi eklendi.
- Konu sahibi için topluluk sonucunu ezmeyen “hâlâ geçerli” bildirimi eklendi.
- Eski state kayıtlarının yeni referans tarihle uyumsuz olması durumunda kullanıcıya eski statü gösterilmesi engellendi.
- Sürüm, neden dağılımı ve son topluluk doğrulama tarihi konu paneline eklendi.
- Güncel çözüm yönlendirmesi aynı konu, yönlendirme konusu, görünmeyen konu ve daha eski konu kontrolleriyle sertleştirildi.
- ACP sağlık ekranına son olumsuz geri bildirimler ve alternatif çözüm önerisi sayısı eklendi.
- Sağlık istatistikleri devre dışı forumlardaki eski state verilerini saymayacak şekilde düzeltildi.
- Eski global forum-ID seçeneği stable yapıdan kaldırıldı.
- Minimum hesap yaşı ve mesaj seçenekleri sayısal ACP alanlarına dönüştürüldü.
- 1.1.0 yükseltme şeması ve otomatik yeniden hesaplama job'u eklendi.
- XenForo 2.3.7+ salt-okunur template metot kısıtlamasına tam uyum için entity API adları `get/is/has/can` sözleşmesine taşındı.
- Yeni anlamlı güncelleme geldiğinde eski moderatör override durumunun yeni doğrulama döngüsüne taşınması engellendi.
- İlk hesaplamada yalnızca eski olumlu oyları bulunan konuların bir tur boyunca güncel görünmesi engellendi; doğrudan yeniden doğrulama uygulanır.
- Soru türü konularda seçilmiş çözüm gönderisi de anlamlı güncelleme hesabına dahil edildi.
- Forum durum filtresi state referans tarihi ve forum bekleme eşiğiyle birebir eşleştirildi; eski state'in yanlış filtre sonucuna girmesi engellendi.
- ACP son olumsuz geri bildirim listesi yalnız etkin forumlar, uygun konular ve mevcut doğrulama döngüsündeki oylarla sınırlandırıldı.
- ACP sağlık ekranına etkin forumlardaki görünür konu sayısı ayrı metrik olarak eklendi.
- Eski regresyon testleri 1.1.0 kaynak paketine geri eklendi ve yeni güvenlik/uyumluluk testleriyle birlikte çalıştırılır hale getirildi.
- Günlük yeniden hesaplama cron'u benzersiz job anahtarıyla kuyruğa alınarak aynı job'un üst üste birikmesi engellendi.
- Orphan kullanıcı temizliği için moderatör ve replacement kullanıcı alanlarına indeks eklendi.
- Yeni doğrulama döngüsünde eski oy alanlarının formda yeniden doldurulması engellendi.
- Moderatör ve replacement yönetimi doğrulama yaş eşiğiyle aynı uygunluk kuralına bağlandı.
- Durum eşikleri birbirleriyle çelişemeyecek şekilde normalize edildi.
- Konu sahibi geçerlilik bildirimi stale state üzerinde çalışmadan önce state yeniden hesaplanır hale getirildi.
- Genel konu listelerinde N+1 sorgu riskini kaldırmak için güncellik rozeti yalnız toplu preload yapılmış listelerde çalışacak şekilde sınırlandırıldı.
- Güncel Çözümler arama girdisi 100 karakterle sınırlandırılarak gereksiz büyük LIKE sorguları engellendi.
- Silinmiş kullanıcı oyları cleanup sırasında kaldırıldığında ilgili aggregate state anında stale işaretlenerek eski oy toplamının kullanıcıya geçerli sonuç gibi gösterilmesi engellendi.

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
