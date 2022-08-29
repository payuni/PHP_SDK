# 目錄
* [環境需求](#環境需求)
* [安裝](#安裝)
* [使用方式](#使用方式)
# 環境需求
* PHP：^7.0 || ^8.0
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
$result = $payuniApi->UniversalTrade($encryptInfo, $mode);
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
  * $merKey
    * 請登入PAYUNi平台檢視商店串接資訊取得 Hash Key
  * $merIV
    * 請登入PAYUNi平台檢視商店串接資訊取得 Hash IV
  * $type (非必填)
    * 連線測試區 => t
  * $mode
    * 整合式支付頁 => upp
    * 虛擬帳號幕後 => atm
    * 超商代碼幕後 => cvs
    * 信用卡幕後　 => credit
    * 交易查詢　　 => trade_query
    * 交易請退款　 => trade_close
    * 交易取消授權 => trade_cancel
    * 信用卡Token(約定) => credit_bind_query
    * 信用卡Token取消(約定/記憶卡號) => credit_bind_cancel
# SAMPLE CODE
* UPP
```php
public function __construct(Request $request)
    {
        $this->merKey = '12345678901234567890123456789012';
        $this->merIV  = '1234567890123456';
        $this->payuniApi = new \Payuni\Sdk\PayuniApi($this->merKey, $this->merIV);
    }
public function upp(){
    $encryptInfo = [
        'MerID' => 'abc',
        'MerTradeNo' => 'test' . date('YmdHis'),
        'TradeAmt' => 100,
        'Timestamp' => time(),
        'ReturnURL' => 'http://www.test.com.tw/api/return',
        'NotifyURL' => 'http://www.test.com.tw/api/notify',
        ...
    ];
    $result = $this->payuniApi->UniversalTrade($encryptInfo, 'upp');
}
public function return(Request $request){
    $postData = $request->all();
    $result = $this->payuniApi->ResultProcess($postData);
}
public function notify(Request $request){
    $postData = $request->all();
    $result = $this->payuniApi->ResultProcess($postData);
}
```
* credit
```php
public function credit(){
    $encryptInfo = [
        'MerID' => 'abc',
        'MerTradeNo' => 'test' . date('YmdHis'),
        'TradeAmt' => 100,
        'CardNo' => '1234567890123456',
        'CardCVC' => '123',
        'CardExpired' => '1230',
        'Timestamp' => time(),
        ...
    ];
    $result = $this->payuniApi->UniversalTrade($encryptInfo, 'credit');
}
```
* atm
```php
public function atm(){
    $encryptInfo = [
        'MerID' => 'abc',
        'MerTradeNo' => 'test' . date('YmdHis'),
        'TradeAmt' => 100,
        'BankType' => '822',
        'Timestamp' => time(),
        ...
    ];
    $result = $this->payuniApi->UniversalTrade($encryptInfo, 'atm');
}
```
* cvs
```php
public function cvs(){
    $encryptInfo = [
        'MerID' => 'abc',
        'MerTradeNo' => 'test' . date('YmdHis'),
        'TradeAmt' => 100,
        'Timestamp' => time(),
        ...
    ];
    $result = $this->payuniApi->UniversalTrade($encryptInfo, 'cvs');
}
```
* 交易查詢
```php
public function tradeQuery(){
    $encryptInfo = [
        'MerID' => 'abc',
        'MerTradeNo' => 'test20220829111528',
        'Timestamp' => time(),
        ...
    ];
    $result = $this->payuniApi->UniversalTrade($encryptInfo, 'trade_query');
}
```
* 交易請退款(credit)
```php
public function tradeClose(){
    $encryptInfo = [
        'MerID' => 'abc',
        'TradeNo' => '16614190477810373246',
        'CloseType' => 1,
        'Timestamp' => time(),
        ...
    ];
    $result = $this->payuniApi->UniversalTrade($encryptInfo, 'trade_close');
}
```
* 交易取消授權(credit)
```php
public function tradeCancel(){
    $encryptInfo = [
        'MerID' => 'abc',
        'TradeNo' => '16614190477810373246',
        'Timestamp' => time()
    ];
    $result = $this->payuniApi->UniversalTrade($encryptInfo, 'trade_cancel');
}
```
* 信用卡Token查詢(約定)(credit)
```php
public function tradeBindQuery(){
    $encryptInfo = [
        'MerID' => 'abc',
        'Timestamp' => time(),
        ...
    ];
    $result = $this->payuniApi->UniversalTrade($encryptInfo, 'trade_bind_query');
}
```

* 信用卡Token取消(約定/記憶卡號)(credit)
```php
public function tradeBindCancel(){
    $encryptInfo = [
        'MerID' => 'abc',
        'Timestamp' => time(),
        'UseTokenType' => 1,
        'BindVal' => '...',
        ...
    ];
    $result = $this->payuniApi->UniversalTrade($encryptInfo, 'trade_bind_cancel');
}
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
