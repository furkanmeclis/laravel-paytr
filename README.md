# Laravel Paytr (Güncel ve Kapsamlı Sürüm)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/furkanmeclis/laravel-paytr.svg?style=flat-square)](https://packagist.org/packages/furkanmeclis/laravel-paytr)
[![Total Downloads](https://img.shields.io/packagist/dt/furkanmeclis/laravel-paytr.svg?style=flat-square)](https://packagist.org/packages/furkanmeclis/laravel-paytr)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=flat-square)](https://opensource.org/licenses/MIT)

Bu paket, **Paytr** ödeme altyapısını modern Laravel (10, 11, 12+) projelerinize kolayca entegre edebilmeniz için geliştirilmiştir. `past/paytr` paketinin kod tabanı kullanılarak **Furkan Meclis** tarafından yeniden yapılandırılmış, güncellenmiş ve bakımı yapılmaktadır.

Paket, Paytr'ın **Iframe API**, **Direct API** ve **Iframe Havale/EFT** gibi tüm popüler ödeme yöntemlerini destekler.

---

## İçindekiler

- [Desteklenen Sürümler](#desteklenen-sürümler)
- [Kurulum](#kurulum)
- [Yapılandırma](#yapılandırma)
  - [Yapılandırma Dosyasını Yayınlama](#yapılandırma-dosyasını-yayınlama)
  - [Ortam Değişkenleri (.env)](#ortam-değişkenleri-env)
- [Temel Kullanım (`FurkanMeclis\Paytr\Payment`)](#temel-kullanım-furkanmeclispaytrpayment)
  - [Iframe API ile Ödeme Alma](#1-iframe-api-ile-ödeme-alma)
  - [Direct API (Doğrudan Ödeme)](#2-direct-api-doğrudan-ödeme)
  - [Iframe Havale/EFT ile Ödeme](#3-iframe-havaleeft-ile-ödeme)
- [Ödeme Bildirimlerini (Callback) Doğrulama](#ödeme-bildirimlerini-callback-doğrulama)
  - [Yöntem 1: `Payment` Sınıfı ile Doğrulama (Önerilen)](#yöntem-1-payment-sınıfı-ile-doğrulama-önerilen)
  - [Yöntem 2: `PaymentVerification` Sınıfı ile Doğrulama](#yöntem-2-paymentverification-sınıfı-ile-doğrulama)
- [Gelişmiş Kullanım ve İpuçları](#gelişmiş-kullanım-ve-ipuçları)
  - [Taksit Seçenekleri](#taksit-seçenekleri)
  - [Para Birimi (Currency) Değiştirme](#para-birimi-currency-değiştirme)
- [Paket Sınıflarına Genel Bakış](#paket-sınıflarına-genel-bakış)
- [Alternatif Kullanım (`Request\Payment`)](#alternatif-kullanım-requestpayment)

---

## Desteklenen Sürümler
- **PHP:** `^8.2`
- **Laravel:** `^10.0`, `^11.0`, `^12.0`

---

## Kurulum

Composer kullanarak paketi projenize kolayca ekleyebilirsiniz:
```bash
composer require furkanmeclis/laravel-paytr
```

---

## Yapılandırma

### Yapılandırma Dosyasını Yayınlama
Paketin yapılandırma dosyasını (`paytr.php`) kendi projenizin `config` dizinine kopyalamak için aşağıdaki Artisan komutunu çalıştırın. Bu, varsayılan ayarları değiştirmenize olanak tanır.

```bash
php artisan vendor:publish --provider="FurkanMeclis\Paytr\PaytrServiceProvider"
```

### Ortam Değişkenleri (.env)
Yapılandırma dosyasını yayınladıktan sonra, `.env` dosyanıza aşağıdaki değişkenleri ekleyip kendi Paytr Mağaza bilgilerinizle doldurmanız gerekmektedir.

```env
# Paytr Mağaza Bilgileri
PAYTR_MERCHANT_ID=
PAYTR_MERCHANT_SALT=
PAYTR_MERCHANT_KEY=

# Geri Dönüş URL'leri
PAYTR_SUCCESS_URL=https://siteniz.com/odeme-basarili
PAYTR_FAIL_URL=https://siteniz.com/odeme-basarisiz

# Diğer Ayarlar
PAYTR_TEST_MODE=true
PAYTR_BASE_URI=https://www.paytr.com
PAYTR_TIMEOUT=60
```

- `PAYTR_TEST_MODE`: `true` olarak ayarlandığında tüm işlemler test modunda çalışır. Canlıya geçerken `false` yapmayı unutmayın.
- `PAYTR_BASE_URI`: Paytr API'sinin ana URL'sidir. Genellikle değiştirmeniz gerekmez.
- `PAYTR_TIMEOUT`: API istekleri için saniye cinsinden zaman aşımı süresi.

---

## Temel Kullanım (`FurkanMeclis\Paytr\Payment`)

Bu sınıf, ödeme işlemleri oluşturmak için ana ve önerilen yöntemdir. Oldukça esnek ve zincirleme metot (`fluent interface`) kullanımını destekler.

### 1. Iframe API ile Ödeme Alma
Bu en yaygın yöntemdir. Kullanıcı, sitenizden ayrılmadan güvenli bir Paytr iframe'i içinde ödeme yapar.

```php
use FurkanMeclis\Paytr\Payment;
use FurkanMeclis\Paytr\Enums\TransactionType;
use FurkanMeclis\Paytr\Request\Basket;
use FurkanMeclis\Paytr\Request\Order;
use FurkanMeclis\Paytr\Request\Product;

public function startPayment()
{
    // 1. Ödeme nesnesini Service Container veya `new` ile oluşturun
    $payment = app(Payment::class); // veya new Payment(config('paytr.credentials'), config('paytr.options'));

    // 2. İşlem tipini ve diğer seçenekleri belirleyin
    $payment->getOption()
            ->setTransactionType(TransactionType::IFRAME)
            ->setTestMode(true); // .env'deki ayarı geçersiz kılar

    // 3. Sepeti ve ürünleri oluşturun
    $basket = new Basket();
    $product1 = (new Product())->setName('T-Shirt')->setPrice(100);
    $product2 = (new Product())->setName('Mug')->setPrice(50.50);
    $basket->addProduct($product1, 1); // 1 adet T-Shirt
    $basket->addProduct($product2, 2); // 2 adet Mug

    // 4. Sipariş ve kullanıcı bilgilerini ayarlayın
    $order = new Order();
    $order->setUserName('Furkan Meclis')
          ->setUserAddress('Test Adres, No: 1, Daire: 2, İstanbul')
          ->setEmail('test@furkanmeclis.com')
          ->setUserPhone('5551234567')
          ->setUserIp(request()->ip())
          ->setMerchantOrderId('SIPARIS' . time())
          ->setPaymentAmount(201.00) // Sepet toplamı ile aynı olmalı: 100 + (50.50 * 2)
          ->setBasket($basket);

    // 5. Siparişi ödeme nesnesine ekleyin ve API'yi çağırın
    $payment->setOrder($order);
    $response = $payment->call()->getResponse();

    if ($response->isSuccess() && $response->isHtml()) {
        // Başarılı olursa, Paytr'dan dönen iframe HTML'ini sayfada gösterin
        return $response->getHtml();
    }
    
    // Hata durumunda
    return "Hata: " . $response->getMessage();
}
```

### 2. Direct API (Doğrudan Ödeme)
Bu yöntemde, kullanıcı kredi kartı bilgilerini doğrudan sitenizdeki formlara girer. **PCI-DSS sertifikası gerektirir!**

```php
// ... (use ifadeleri önceki örnekle aynı)

// İşlem tipini DIRECT olarak değiştirin
$payment->getOption()->setTransactionType(TransactionType::DIRECT);

// Sipariş oluştururken kart bilgilerini de ekleyin
$order->setCardOwner("TEST KULLANICI")
      ->setCardNumber("4355084355084358") // Test kart numarası
      ->setCardExpireMonth("12")
      ->setCardExpireYear("24")
      ->setCardCvv("000");
      // ... (diğer kullanıcı bilgileri)

// API çağrısı ve yanıtı işleme
$response = $payment->call()->getResponse();

if ($response->isSuccess()) {
    // Direct API'den JSON yanıt döner
    return response()->json($response->getContent());
}

return response()->json(['error' => $response->getMessage()], 400);
```

### 3. Iframe Havale/EFT ile Ödeme

```php
// ... (use ifadeleri önceki örnekle aynı)

// İşlem tipini IFRAME_TRANSFER olarak değiştirin
$payment->getOption()->setTransactionType(TransactionType::IFRAME_TRANSFER);

// Kart bilgileri gerekmez, bu yüzden Order nesnesinden kartla ilgili satırları kaldırabilirsiniz.
// ... (siparişin geri kalanını ayarlayın)

$response = $payment->call()->getResponse();

if ($response->isSuccess() && $response->isHtml()) {
    // Başarılı olursa, Paytr'dan dönen havale/EFT talimatlarını içeren iframe'i gösterin
    return $response->getHtml();
}
```

---

## Ödeme Bildirimlerini (Callback) Doğrulama

Paytr, ödeme sonucu ne olursa olsun `.env` dosyanızda belirttiğiniz `PAYTR_SUCCESS_URL` veya `PAYTR_FAIL_URL`'e kullanıcıyı yönlendirir ve **arka planda** sunucunuza bir `POST` isteği gönderir. Gelen bu isteğin Paytr'dan geldiğini ve değiştirilmediğini doğrulamak zorunludur.

Aşağıda `routes/web.php` veya `routes/api.php` içinde bir callback rotası örneği bulunmaktadır.

### Yöntem 1: `Payment` Sınıfı ile Doğrulama (Önerilen)
Bu yöntem, ana `Payment` sınıfının `checkHash` metodunu kullanır ve en basit yaklaşımdır.

```php
// routes/web.php

use Illuminate\Http\Request;
use FurkanMeclis\Paytr\Payment;

Route::post('/paytr-callback', function () {
    $payment = app(Payment::class);
    
    // 1. Hash kontrolü yap
    if (!$payment->checkHash()) {
        return response('HASH MISMATCH', 401);
    }

    // 2. Gelen isteği al ve durumu kontrol et
    $callbackData = request()->all();

    if ($callbackData['status'] === 'success') {
        // Ödeme başarılı. Sipariş durumunu veritabanında güncelle.
        // $orderId = $callbackData['merchant_oid'];
        // ...
    } else {
        // Ödeme başarısız. Hata mesajını logla.
        // $errorMessage = $callbackData['failed_reason_msg'];
        // ...
    }

    // 3. Paytr'a "OK" yanıtı gönder. Bu zorunludur.
    return response('OK', 200);
});
```

### Yöntem 2: `PaymentVerification` Sınıfı ile Doğrulama
Bu, paketin eski yapısından gelen alternatif bir doğrulama yöntemidir.

```php
// routes/web.php

use Illuminate\Http\Request;
use FurkanMeclis\Paytr\Request\PaymentVerification;

Route::post('/paytr-callback', function (Request $request) {
    $verification = new PaymentVerification($request);

    // 1. Hash kontrolü yap
    if (!$verification->isVerified()) {
        return response('HASH MISMATCH', 401);
    }
    
    // 2. Durumu kontrol et
    if ($verification->isSuccess()) {
        // Ödeme başarılı.
        // $orderId = $verification->getMerchantOid();
    } else {
        // Ödeme başarısız.
        // $errorMessage = $verification->getFailedReasonMessage();
    }

    // 3. Paytr'a "OK" yanıtı gönder.
    return $verification->getProcessedResponse();
});
```
---

## Gelişmiş Kullanım ve İpuçları

### Taksit Seçenekleri
- `setInstallmentCount(int $count)`: Sadece Direct API için geçerlidir. `1` (tek çekim) veya `2-12` arası taksit sayısını belirtir.
- `setNoInstallment(bool $status)`: Iframe API'de taksit seçeneklerini gizlemek için `true` yapın.
- `setMaxInstallment(int $count)`: Iframe API'de gösterilecek maksimum taksit sayısını belirler (`0` hepsi demektir).

```php
// Iframe'de sadece en fazla 6 taksit göster
$payment->getOption()->setMaxInstallment(6);

// Direct API'de 3 taksit yap
$payment->getOption()->setInstallmentCount(3);
```

### Para Birimi (Currency) Değiştirme
Varsayılan para birimi TL'dir. Desteklenen diğer para birimlerini `Currency` enum'ı ile ayarlayabilirsiniz.

```php
use FurkanMeclis\Paytr\Enums\Currency;

$payment->getOption()->setCurrency(Currency::EUR);
```

---

## Paket Sınıflarına Genel Bakış
- `FurkanMeclis\Paytr\Payment`: Ana ödeme sınıfı.
- `FurkanMeclis\Paytr\PaytrClient`: API istekleri için temel Guzzle istemcisi.
- `FurkanMeclis\Paytr\Request\Order`: Sipariş detaylarını ve kullanıcı bilgilerini tutar.
- `FurkanMeclis\Paytr\Request\Basket`: Ürün listesini yönetir.
- `FurkanMeclis\Paytr\Request\Product`: Tek bir ürünün adını ve fiyatını tutar.
- `FurkanMeclis\Paytr\Response\PaymentResponse`: API'den dönen yanıtı (HTML veya JSON) yönetir.
- `FurkanMeclis\Paytr\Enums\*`: `Currency`, `TransactionType` gibi sabit değerleri içeren Enum sınıfları.

---

## Alternatif Kullanım (`Request\Payment`)

Paket, `past/paytr`'ın orijinal yapısını korumak amacıyla `FurkanMeclis\Paytr\Request\Payment` adında alternatif bir ödeme sınıfı daha içerir. Bu sınıfın kullanımı, ana `Payment` sınıfından farklıdır ve zincirleme metotları desteklemez.

> **Uyarı:** Bu sınıf, ana `Payment` sınıfıyla benzer işlevlere sahip olduğu için kafa karıştırıcı olabilir. Genellikle ana `Payment` sınıfını kullanmanız önerilir.

**Örnek:**
```php
use FurkanMeclis\Paytr\Request\Payment as RequestPayment;

$paymentRequest = new RequestPayment();
$paymentRequest->setUserIp(request()->ip());
$paymentRequest->setMerchantOid('SIPARIS_ALT_123');
// ... tüm diğer bilgileri setter metotları ile tek tek ayarlayın ...
$response = $paymentRequest->create();
```
