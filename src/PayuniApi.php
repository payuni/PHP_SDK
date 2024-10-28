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
    private $attributes = [];
    public $encryptInfo, $merKey, $merIV, $apiUrl, $parameter, $curlUrl;
    public function __construct(string $key, string $iv, string $type = '')
    {
        $this->encryptInfo = [];
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
            'Version'     => '',
            'EncryptInfo' => '',
            'HashInfo'    => '',
        ];
    }
    /**
     * 呼叫各類api
     * @author    Yifan
     * @ dateTime 2022-08-23
     */
    public function UniversalTrade(array $encryptInfo, string $tradeType, string $version = '1.0')
    {
        $this->encryptInfo = $encryptInfo;
        $this->parameter['Version'] = $version;
        $contrast = [
            'upp'                  => 'upp',
            'atm'                  => 'atm',
            'cvs'                  => 'cvs',
            'credit'               => 'credit',
            'linepay'              => 'linepay',
            'aftee_direct'         => 'aftee_direct',
            'trade_query'          => 'trade/query',
            'trade_close'          => 'trade/close',
            'trade_cancel'         => 'trade/cancel',
            'cancel_cvs'           => 'cancel_cvs',
            'credit_bind_query'    => 'credit_bind/query',
            'credit_bind_cancel'   => 'credit_bind/cancel',
            'trade_refund_icash'   => 'trade/common/refund/icash',
            'trade_refund_aftee'   => 'trade/common/refund/aftee',
            'trade_confirm_aftee'  => 'trade/common/confirm/aftee',
            'trade_refund_linepay' => 'trade/common/refund/linepay',
        ];
        $checkArr = $this->CheckParams();
        if ($checkArr['success']) {
            try {
                switch ($tradeType) {
                    case 'upp': // 交易建立 整合式支付頁
                    case 'atm': // 交易建立 虛擬帳號幕後
                    case 'cvs': // 交易建立 超商代碼幕後
                    case 'linepay': // 交易建立 LINE Pay幕後
                    case 'aftee_direct': // 交易建立 AFTEE幕後
                        if ('linepay' == $tradeType) {
                            $this->parameter['Version'] = '1.1';
                        }
                        if (empty($this->encryptInfo['MerTradeNo'])) {
                            throw new Exception('商店訂單編號為必填(MerTradeNo is not setting)');
                        }
                        if (empty($this->encryptInfo['TradeAmt'])) {
                            throw new Exception('訂單金額為必填(TradeAmt is not setting)');
                        }
                        break;
                    case 'credit': // 交易建立 信用卡幕後
                        if (empty($this->encryptInfo['MerTradeNo'])) {
                            throw new Exception('商店訂單編號為必填(MerTradeNo is not setting)');
                        }
                        if (empty($this->encryptInfo['TradeAmt'])) {
                            throw new Exception('訂單金額為必填(TradeAmt is not setting)');
                        }
                        if (!isset($this->encryptInfo['CreditHash'])) {
                            if ($this->encryptInfo['CardNo'] == null || $this->encryptInfo['CardNo'] == '') {
                                throw new Exception('信用卡卡號為必填(CardNo is not setting)');
                            }
                            if ($this->encryptInfo['CardExpired'] == null || $this->encryptInfo['CardExpired'] == '') {
                                throw new Exception('信用卡到期年月為必填(CardExpired is not setting)');
                            }
                            if ($this->encryptInfo['CardCVC'] == null || $this->encryptInfo['CardCVC'] == '') {
                                throw new Exception('信用卡安全碼為必填(CardCVC is not setting)');
                            }
                        }
                        break;
                    case 'trade_close': // 交易請退款
                        if (empty($this->encryptInfo['TradeNo'])) {
                            throw new Exception('uni序號為必填(TradeNo is not setting)');
                        }
                        if (empty($this->encryptInfo['CloseType'])) {
                            throw new Exception('關帳類型為必填(CloseType is not setting)');
                        }
                        break;
                    case 'trade_cancel': // 交易取消授權
                    case 'trade_confirm_aftee': // 後支付確認(AFTEE)
                        if (empty($this->encryptInfo['TradeNo'])) {
                            throw new Exception('uni序號為必填(TradeNo is not setting)');
                        }
                        break;
                    case 'cancel_cvs': // 交易取消超商代碼(CVS)
                        if (empty($this->encryptInfo['PayNo'])) {
                            throw new Exception('超商代碼為必填(PayNo is not setting)');
                        }
                        break;
                    case 'credit_bind_cancel': // 信用卡token取消(約定/記憶卡號)
                        if (empty($this->encryptInfo['UseTokenType'])) {
                            throw new Exception('信用卡Token類型為必填(UseTokenType is not setting)');
                        }
                        if (empty($this->encryptInfo['BindVal'])) {
                            throw new Exception('綁定回傳值 /信用卡Token(BindVal is not setting)');
                        }
                        break;
                    case 'trade_refund_icash': // 愛金卡退款(ICASH)
                    case 'trade_refund_aftee': // 後支付退款(AFTEE)
                    case 'trade_refund_linepay': // LINE Pay退款(LINE)
                        if (empty($this->encryptInfo['TradeNo'])) {
                            throw new Exception('uni序號為必填(TradeNo is not setting)');
                        }
                        if (empty($this->encryptInfo['TradeAmt'])) {
                            throw new Exception('訂單金額為必填(TradeAmt is not setting)');
                        }
                        break;
                    case 'trade_query': // 交易查詢
                    case 'credit_bind_query': // 信用卡token查詢(約定)
                        break;
                    default:
                        throw new Exception('未提供該參數類型(Unknown params)');
                        break;
                }
                $this->SetParams($contrast[$tradeType]);
                if ($tradeType == 'upp') {
                    $this->HtmlApi();
                    exit;
                } else {
                    $result = $this->CurlApi();
                    return $this->ResultProcess($result);
                }
            } catch (Exception $e) {
                return ['success' => false, 'message' => $e->getMessage()];
            }
        } else {
            return $checkArr;
        }
    }
    /**
     * 處理api回傳的結果
     * @ author    Yifan
     * @ dateTime 2022-08-26
     */
    public function ResultProcess($result)
    {
        try {
            if (is_array($result)) {
                $resultArr = $result;
            } else {
                $resultArr = json_decode($result, true);
                if (!is_array($resultArr)) {
                    throw new Exception('傳入參數需為陣列(Result must be an array)');
                }
            }
            if ("ERROR" == $resultArr['Status']) {
                return ['success' => true, 'message' => $resultArr];
            }
            if (isset($resultArr['EncryptInfo'])) {
                if (isset($resultArr['HashInfo'])) {
                    $chkHash = $this->HashInfo($resultArr['EncryptInfo']);
                    if ($chkHash != $resultArr['HashInfo']) {
                        throw new Exception('Hash值比對失敗(Hash mismatch)');
                    }
                    $resultArr['EncryptInfo'] = $this->Decrypt($resultArr['EncryptInfo']);
                    return ['success' => true, 'message' => $resultArr];
                } else {
                    throw new Exception('缺少Hash資訊(missing HashInfo)');
                }
            } else {
                throw new Exception('缺少加密字串(missing EncryptInfo)');
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            switch ($resultArr['Status']) {
                case 'API00003':
                    $message = '無API版本號';
                    break;
            }
            return ['success' => false, 'message' => $message];
        }
    }
    /**
     * 檢查必填參數是否存在
     * @ author    Yifan
     * @ dateTime  2022-08-23
     */
    private function CheckParams()
    {
        try {
            if (empty($this->encryptInfo['MerID'])) {
                throw new Exception('商店代號為必填(MerID is not setting)');
            }
            if (empty($this->encryptInfo['Timestamp'])) {
                throw new Exception('時間戳記為必填(Timestamp is not setting)');
            }
            return ['success' => true, 'message' => 'params is set correctly'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    /**
     * 設定要curl的參數
     * @author    Yifan
     * @ dateTime 2022-08-23
     */
    private function SetParams(string $type = '')
    {
        $isPlatForm = '';
        if (!empty($this->encryptInfo['IsPlatForm'])) {
            $isPlatForm = $this->encryptInfo['IsPlatForm'];
            unset($this->encryptInfo['IsPlatForm']);
        }
        $this->parameter['MerID']       = $this->encryptInfo['MerID'];
        $this->parameter['EncryptInfo'] = $this->Encrypt();
        $this->parameter['HashInfo']    = $this->HashInfo($this->parameter['EncryptInfo']);
        $this->parameter['IsPlatForm']  = $isPlatForm;
        $this->curlUrl = $this->apiUrl . $type;
    }
    /**
     * 前景呼叫
     * @ author    Yifan
     * @ dateTime 2022-08-25
     */
    private function HtmlApi()
    {
        $htmlprint  = "<html><body onload='document.getElementById(\"upp\").submit();'>";
        $htmlprint .= "<form action='" . $this->curlUrl . "' method='post' id='upp'>";
        foreach ($this->parameter as $key => $value) {
            $htmlprint .= "<input name='" . $key . "' type='hidden' value='" . $value . "' />";
        }
        $htmlprint .= "</form></body></html>";
        echo $htmlprint;
    }
    /**
     * CURL
     * @ author   Yifan
     * @ dateTime 2022-08-23
     */
    private function CurlApi()
    {
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
    private function Encrypt()
    {
        $tag = '';
        $encrypted = openssl_encrypt(http_build_query($this->encryptInfo), 'aes-256-gcm', trim($this->merKey), 0, trim($this->merIV), $tag);
        return trim(bin2hex($encrypted . ':::' . base64_encode($tag)));
    }
    /**
     * 解密
     */
    private function Decrypt(string $encryptStr = '')
    {
        list($encryptData, $tag) = explode(':::', hex2bin($encryptStr), 2);
        $encryptInfo = openssl_decrypt($encryptData, 'aes-256-gcm', trim($this->merKey), 0, trim($this->merIV), base64_decode($tag));
        parse_str($encryptInfo, $encryptArr);
        return $encryptArr;
    }
    /**
     * hash
     */
    private function HashInfo(string $encryptStr = '')
    {
        return strtoupper(hash('sha256', $this->merKey . $encryptStr . $this->merIV));
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }
}
