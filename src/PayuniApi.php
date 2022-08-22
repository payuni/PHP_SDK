<?php
namespace Payuni\Sdk;
use Exception;
class PayuniApi
{
    public function __construct(array $encryptInfo, string $key, string $iv, string $type = 't')
    {
        $this->encryptInfo = $encryptInfo;
        $this->key  = $key;
        $this->iv   = $iv;
        $this->type = $type;
    }
    public function test(){
        $checkArr = $this->checkParams();
        if( $checkArr['success'] ) {
            echo 'test payuni api';
        }
        else {
            echo $checkArr['message'];
        }
    }
    public function checkParams() {
        try {
            if ($this->encryptInfo['MerID'] == null || $this->encryptInfo['MerID'] == '') {
                throw new Exception('MerID');
            }
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}