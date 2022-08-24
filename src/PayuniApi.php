<?php
namespace Payuni\Sdk;
use Exception;

class PayuniApi
{
    public function __construct(array $encryptInfo, string $key, string $iv, string $type = 't')
    {
        $this->encryptInfo = $encryptInfo;
        $this->merKey = $key;
        $this->merIV  = $iv;
        $this->apiUrl = "api.payuni.com.tw/api/";
        $this->apiUrl = "lapi-epay.presco.com.tw/api/";
        $prefix       = "https://";
        if ($type == 't') {
            $prefix .= "sandbox-";
        }
        $prefix       = "http://";
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
    public function UniversalTrade(string $tradeType) {
        $checkArr = $this->CheckParams();
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
        ];
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
                        if ($this->EncryptInfo['MerTradeNo'] == null || $this->EncryptInfo['MerTradeNo'] == '') {
                            throw new Exception('MerTradeNo is not setting');
                        }
                        if ($this->EncryptInfo['TradeAmt'] == null || $this->EncryptInfo['TradeAmt'] == '') {
                            throw new Exception('TradeAmt is not setting');
                        }
                        if ($this->EncryptInfo['CardNo'] == null || $this->EncryptInfo['CardNo'] == '') {
                            throw new Exception('CardNo is not setting');
                        }
                        if ($this->EncryptInfo['CardCVC'] == null || $this->EncryptInfo['CardCVC'] == '') {
                            throw new Exception('CardCVC is not setting');
                        }
                        break;
                    case 'trade_close': // 交易請退款
                        if ($this->EncryptInfo['TradeNo'] == null || $this->EncryptInfo['TradeNo'] == '') {
                            throw new Exception('TradeNo is not setting');
                        }
                        if ($this->EncryptInfo['CloseType'] == null || $this->EncryptInfo['CloseType'] == '') {
                            throw new Exception('CloseType is not setting');
                        }
                        break;
                    case 'trade_cancel': // 交易取消授權
                        if ($this->EncryptInfo['TradeNo'] == null || $this->EncryptInfo['TradeNo'] == '') {
                            throw new Exception('TradeNo is not setting');
                        }
                        break;
                    case 'credit_bind_cancel': // 信用卡token取消(約定/記憶卡號)
                        if ($this->EncryptInfo['UseTokenType'] == null || $this->EncryptInfo['UseTokenType'] == '') {
                            throw new Exception('UseTokenType is not setting');
                        }
                        if ($this->EncryptInfo['BindVal'] == null || $this->EncryptInfo['BindVal'] == '') {
                            throw new Exception('BindVal is not setting');
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
                $resultArr = $this->CurlApi();
                $resultArr['ResultInfo'] = json_decode($resultArr['ResultInfo'], true);
                $chkHash = $this->HashInfo($resultArr['ResultInfo']['EncryptInfo']);
                if ( $chkHash != $resultArr['ResultInfo']['HashInfo']) {
                    throw new Exception('Hash mismatch');
                }
                $resultArr['ResultInfo']['EncryptInfo'] = $this->Decrypt($resultArr['ResultInfo']['EncryptInfo']);
                return ['success' => true, 'message' => $resultArr];
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
     * 檢查必填參數是否存在
     * @ author    Yifan
     * @ dateTime  2022-08-23
     */
    public function CheckParams() {
        try {
            if ($this->encryptInfo['MerID'] == null || $this->encryptInfo['MerID'] == '') {
                throw new Exception('MerID is not setting');
            }
            if ($this->encryptInfo['Timestamp'] == null || $this->encryptInfo['Timestamp'] == '') {
                throw new Exception('Timestamp is not setting');
            }
            return ['success' => true, 'message' => ''];
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
    public function SetParams(string $type = '') {
        $this->parameter['MerID'] = $this->encryptInfo['MerID'];
        $this->parameter['EncryptInfo'] = $this->Encrypt();
        $this->parameter['HashInfo']    = $this->HashInfo($this->parameter['EncryptInfo']);
        $this->apiUrl = $this->apiUrl . $type;
    }
    /**
     * CURL
     * @ author   Yifan
     * @ dateTime 2022-08-23
     */
    public function CurlApi() {
        $curlError = '-';
        $curlOptions = array(
            CURLOPT_URL            => $this->apiUrl,
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
        $retCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_errno($ch);
        curl_close($ch);

        $returnInfo = array(
            'URL'           => $this->apiUrl,
            'HttpStatus'    => $retCode,
            'CurlErrorNo'   => $curlError,
            'ResultInfo'    => $result,
        );

        return $returnInfo;
    }
    /**
     * 加密
     *
     */
    function Encrypt() {
        $tag = '';
        $encrypted = openssl_encrypt(http_build_query($this->encryptInfo), 'aes-256-gcm', trim($this->merKey), 0, trim($this->merIV), $tag);
        return trim(bin2hex($encrypted . ':::' . base64_encode($tag)));
    }
    /**
     * 解密
     */
    function Decrypt(string $encryptStr = '') {
        list($encryptData, $tag) = explode(':::', hex2bin($encryptStr), 2);
        $encryptInfo = openssl_decrypt($encryptData, 'aes-256-gcm', trim($this->merKey), 0, trim($this->merIV), base64_decode($tag));
        parse_str($encryptInfo, $encryptArr);
        return $encryptArr;
    }
    /**
     * hash
     */
    public function HashInfo(string $encryptStr = '') {
        return strtoupper(hash('sha256', $this->merKey.$encryptStr.$this->merIV));
    }
}