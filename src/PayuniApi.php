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
        $this->checkParams();
        echo 'test payuni api';
    }
    public function checkParams() {
        try {
            if ($this->encryptInfo['MerID'] == null || $this->encryptInfo['MerID'] == '') {
                throw new Exception('You need to supply the MerID or MerID');
            }
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }
}