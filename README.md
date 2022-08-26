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
```php
$payuniApi = new \Payuni\Sdk\PayuniApi($encryptInfo, $merKey, $merIV);
$result = $payuniApi->UniversalTrade('<the api type code>');
```
* type code kind
  * 整合式支付頁 => upp
  * 虛擬帳號幕後 => atm
  * 超商代碼幕後 => cvs
  * 信用卡幕後　 => credit
  * 交易查詢　　 => trade_query
  * 交易請退款　 => trade_close
  * 交易取消授權 => trade_cancel
  * 信用卡Token(約定) => credit_bind_query
  * 信用卡Token取消(約定/記憶卡號) => credit_bind_cancel
