<?php
/**
 * Copyright 2022 PRESCO. All rights reserved.

 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Payuni\Sdk;
use Exception;

class PayuniApi
{
    public function __construct(string $key, string $iv, string $type = '')
    {
        $this->encryptInfo = '';
        $this->merKey = $key;
        $this->merIV  = $iv;
        $this->apiUrl = "api.payuni.com.tw/api/";
        $prefix       = "https://";
        if ($type == 't') {
            $prefix .= "sandbox-";
        }
        $this->apiUrl = $prefix . $this->apiUrl;

        $this->parameter = [
            'MerID'       => '',
            'Version'     => '1.0',
            'EncryptInfo' => '',
            'HashInfo'    => '',
        ];
    }
    /**
     * 呼叫各類api
     * @author    Yifan
     * @ dateTime 2022-08-23
     */
    public function UniversalTrade(array $encryptInfo, string $tradeType) {
        $this->encryptInfo = $encryptInfo;
        $contrast = [
            'upp' => 'upp',
            'atm' => 'atm',
            'cvs' => 'cvs',
            'credit' => 'credit',
            'trade_query' => 'trade/query',
            'trade_close' => 'trade/close',
            'trade_cancel' => 'trade/cancel',
            'credit_bind_query' => 'credit_bind/query',
            'credit_bind_cancel' => 'credit_bind/cancel',
            'trade_refund_icash' => 'trade/common/refund/icash',
        ];
        $checkArr = $this->CheckParams();
        if ( $checkArr['success'] ) {
            try {
                switch ( $tradeType ) {
                    case 'upp': // 交易建立 整合式支付頁
                    case 'atm': // 交易建立 虛擬帳號幕後
                    case 'cvs': // 交易建立 超商代碼幕後
                        if ($this->encryptInfo['MerTradeNo'] == null || $this->encryptInfo['MerTradeNo'] == '') {
                            throw new Exception('MerTradeNo is not setting');
                        }
                        if ($this->encryptInfo['TradeAmt'] == null || $this->encryptInfo['TradeAmt'] == '') {
                            throw new Exception('TradeAmt is not setting');
                        }
                        break;
                    case 'credit': // 交易建立 信用卡幕後
                        if ($this->encryptInfo['MerTradeNo'] == null || $this->encryptInfo['MerTradeNo'] == '') {
                            throw new Exception('MerTradeNo is not setting');
                        }
                        if ($this->encryptInfo['TradeAmt'] == null || $this->encryptInfo['TradeAmt'] == '') {
                            throw new Exception('TradeAmt is not setting');
                        }
                        if ($this->encryptInfo['CardNo'] == null || $this->encryptInfo['CardNo'] == '') {
                            throw new Exception('CardNo is not setting');
                        }
                        if ($this->encryptInfo['CardCVC'] == null || $this->encryptInfo['CardCVC'] == '') {
                            throw new Exception('CardCVC is not setting');
                        }
                        break;
                    case 'trade_close': // 交易請退款
                        if ($this->encryptInfo['TradeNo'] == null || $this->encryptInfo['TradeNo'] == '') {
                            throw new Exception('TradeNo is not setting');
                        }
                        if ($this->encryptInfo['CloseType'] == null || $this->encryptInfo['CloseType'] == '') {
                            throw new Exception('CloseType is not setting');
                        }
                        break;
                    case 'trade_cancel': // 交易取消授權
                        if ($this->encryptInfo['TradeNo'] == null || $this->encryptInfo['TradeNo'] == '') {
                            throw new Exception('TradeNo is not setting');
                        }
                        break;
                    case 'credit_bind_cancel': // 信用卡token取消(約定/記憶卡號)
                        if ($this->encryptInfo['UseTokenType'] == null || $this->encryptInfo['UseTokenType'] == '') {
                            throw new Exception('UseTokenType is not setting');
                        }
                        if ($this->encryptInfo['BindVal'] == null || $this->encryptInfo['BindVal'] == '') {
                            throw new Exception('BindVal is not setting');
                        }
                        break;
                    case 'trade_refund_icash': // 愛金卡退款(ICASH)
                        if ($this->encryptInfo['TradeNo'] == null || $this->encryptInfo['TradeNo'] == '') {
                            throw new Exception('TradeNo is not setting');
                        }
                        if ($this->encryptInfo['TradeAmt'] == null || $this->encryptInfo['TradeAmt'] == '') {
                            throw new Exception('TradeAmt is not setting');
                        }
                        break;
                    case 'trade_query': // 交易查詢
                    case 'credit_bind_query': // 信用卡token查詢(約定)
                        break;
                    default:
                        throw new Exception('Unknown params');
                        break;
                }
                $this->SetParams($contrast[$tradeType]);
                if ($tradeType == 'upp') {
                    $this->HtmlApi();
                    exit;
                }
                else {
                    $result = $this->CurlApi();
                    return $this->ResultProcess($result);
                }
            }
            catch ( Exception $e ) {
                return ['success' => false, 'message' => $e->getMessage()];
            }
        }
        else {
            return $checkArr;
        }
    }
    /**
     * 處理api回傳的結果
     * @ author    Yifan
     * @ dateTime 2022-08-26
     */
    public function ResultProcess($result) {
        try {
            if (is_array($result)) {
                $resultArr = $result;
            }
            else {
                $resultArr = json_decode($result, true);
                if (!is_array($resultArr)){
                    throw new Exception('Result must be an array');
                }
            }
            if (isset($resultArr['EncryptInfo'])){
                if (isset($resultArr['HashInfo'])){
                    $chkHash = $this->HashInfo($resultArr['EncryptInfo']);
                    if ( $chkHash != $resultArr['HashInfo'] ) {
                        throw new Exception('Hash mismatch');
                    }
                    $resultArr['EncryptInfo'] = $this->Decrypt($resultArr['EncryptInfo']);
                    return ['success' => true, 'message' => $resultArr];
                }
                else {
                    throw new Exception('missing HashInfo');
                }
            }
            else {
                throw new Exception('missing EncryptInfo');
            }
        }
        catch ( Exception $e ) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    /**
     * 檢查必填參數是否存在
     * @ author    Yifan
     * @ dateTime  2022-08-23
     */
    private function CheckParams() {
        try {
            if ($this->encryptInfo['MerID'] == null || $this->encryptInfo['MerID'] == '') {
                throw new Exception('MerID is not setting');
            }
            if ($this->encryptInfo['Timestamp'] == null || $this->encryptInfo['Timestamp'] == '') {
                throw new Exception('Timestamp is not setting');
            }
            return ['success' => true, 'message' => 'params is set correctly'];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    /**
     * 設定要curl的參數
     * @author    Yifan
     * @ dateTime 2022-08-23
     */
    private function SetParams(string $type = '') {
        $this->parameter['MerID']       = $this->encryptInfo['MerID'];
        $this->parameter['EncryptInfo'] = $this->Encrypt();
        $this->parameter['HashInfo']    = $this->HashInfo($this->parameter['EncryptInfo']);
        $this->curlUrl = $this->apiUrl . $type;
    }
    /**
     * 前景呼叫
     * @ author    Yifan
     * @ dateTime 2022-08-25
     */
    private function HtmlApi() {
        $htmlprint  = "<html><body onload='document.getElementById(\"upp\").submit();'>";
        $htmlprint .= "<form action='".$this->curlUrl."' method='post' id='upp'>";
        $htmlprint .= "<input name='MerID' type='hidden' value='".$this->parameter['MerID']."' />";
        $htmlprint .= "<input name='Version' type='hidden' value='".$this->parameter['Version']."' />";
        $htmlprint .= "<input name='EncryptInfo' type='hidden' value='".$this->parameter['EncryptInfo']."' />";
        $htmlprint .= "<input name='HashInfo' type='hidden' value='".$this->parameter['HashInfo']."' />";
        $htmlprint .= "</form></body></html>";
        echo $htmlprint;
    }
    /**
     * CURL
     * @ author   Yifan
     * @ dateTime 2022-08-23
     */
    private function CurlApi() {
        $curlOptions = array(
            CURLOPT_URL            => $this->curlUrl,
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => 'PRESCOSDKAPI',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $this->parameter,
        );
        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        $result    = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
    /**
     * 加密
     *
     */
    private function Encrypt() {
        $tag = '';
        $encrypted = openssl_encrypt(http_build_query($this->encryptInfo), 'aes-256-gcm', trim($this->merKey), 0, trim($this->merIV), $tag);
        return trim(bin2hex($encrypted . ':::' . base64_encode($tag)));
    }
    /**
     * 解密
     */
    private function Decrypt(string $encryptStr = '') {
        list($encryptData, $tag) = explode(':::', hex2bin($encryptStr), 2);
        $encryptInfo = openssl_decrypt($encryptData, 'aes-256-gcm', trim($this->merKey), 0, trim($this->merIV), base64_decode($tag));
        parse_str($encryptInfo, $encryptArr);
        return $encryptArr;
    }
    /**
     * hash
     */
    private function HashInfo(string $encryptStr = '') {
        return strtoupper(hash('sha256', $this->merKey.$encryptStr.$this->merIV));
    }
}