<?php
namespace Payuni\Sdk;
use Exception;
class PayuniApi
{
    public function __construct(array $encryptInfo, string $key, string $iv, string $type = 't')
    {
        try {
            if(!isset($encryptInfo['merID'])) {
                throw new Exception('is not have merID');
            }
        }
        catch(Exception $e) {
            return $e->getMessage();
        }
    }
    public function test(){
        echo "this is test payuniApi";
    }
}
