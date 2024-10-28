# 目錄
* [環境需求](#環境需求)
* [安裝](#安裝)
* [使用方式](#使用方式)
# 環境需求
* PHP：^7.1 || ^8.0
# 安裝
請使用[Composer](https://getcomposer.org/)安裝
```bash
composer require payuni/sdk
```
# 使用方式
* 正式區
```php
$payuniApi = new \Payuni\Sdk\PayuniApi($merKey, $merIV);
```
* 測試區
```php
$payuniApi = new \Payuni\Sdk\PayuniApi($merKey, $merIV, $type);
```
* API串接
```php
$result = $payuniApi->UniversalTrade($encryptInfo, $mode, $version);
```
* upp ReturnURL、NotifyURL接收到回傳參數後處理方式
```php
$result = $payuniApi->ResultProcess($requestData);
```
* 參數說明
  * $encryptInfo
    * 參數詳細內容請參考[統一金流API串接文件](https://www.payuni.com.tw/docs/web/#/7/34)對應功能請求參數的EncryptInfo
  ```php
  $encryptInfo = [
            'MerID' => 'ABC',
            'Timestamp' => time(),
            ...
        ];
  ```
  * 若要使用代理商功能請在encryptInfo裡多加上IsPlatForm參數且值給1
  ```php
  $encryptInfo = [
            'IsPlatForm' => 1,
            'MerID' => 'ABC',
            'Timestamp' => time(),
            ...
        ];
  ```
  * $merKey
    * 請登入PAYUNi平台檢視商店串接資訊取得 Hash Key
  * $merIV
    * 請登入PAYUNi平台檢視商店串接資訊取得 Hash IV
  * $type (非必填)
    * 連線測試區 => t
    * 連線正式區 => 不給該參數或給空值
  * $mode
    * 整合式支付頁  => upp
    * 虛擬帳號幕後  => atm
    * 超商代碼幕後  => cvs
    * 信用卡幕後　  => credit
    * LINE Pay幕後 => linepay
    * AFTEE幕後    => aftee_direct
    * 交易查詢　 　 => trade_query
    * 交易請退款 　 => trade_close
    * 交易取消授權  => trade_cancel
    * 信用卡Token(約定) => credit_bind_query
    * 信用卡Token取消(約定/記憶卡號) => credit_bind_cancel
    * 交易取消超商代碼(CVS) => cancel_cvs
    * 後支付確認(AFTEE) => trade_confirm_aftee
    * 愛金卡退款(ICASH) => trade_refund_icash
    * 後支付退款(AFTEE) => trade_refund_aftee
    * LINE Pay退款(LINE) => trade_refund_linepay

  * $version (非必填)
    * 所呼叫API的版本號，預設1.0(LINE Pay預設為1.1)
    * 若需呼叫其他版本號，該參數請給字串，e.g. $payuni->UniversalTrade($encryptInfo, 'trade_query','2.0');，而非給數字或浮點數
    * 若需呼叫新版號請參考[統一金流API串接文件](https://www.payuni.com.tw/docs/web/#/7/34)對應功能請求參數的Version備註
* 其餘請參考[範例](https://github.com/payuni/PHP_SDK/tree/main/examples)

* 原生php
  * **your file path** => 請自行填入程式所放置之路徑
```php
namespace Payuni\Sdk;
require_once('<your file path>/PayuniApi.php');
$merKey = '12345678901234567890123456789012';
$merIV  = '1234567890123456';
$payuni = new PayuniApi($merKey, $merIV);

$encryptInfo = [
    'MerID' => 'ABC',
    'TradeNo'   => '16614190477810373246',
    'Timestamp' => time()
];
$result = $payuni->UniversalTrade($encryptInfo, 'trade_query');
```
# LICENSE
```text
Copyright 2022 PRESCO. All rights reserved.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```
