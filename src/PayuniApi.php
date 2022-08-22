<?php
namespace Payuni\Sdk;
use Exception;
class PayuniApi
{
    public function __construct(array $encryptInfo, string $key, string $iv, string $type = 't')
    {
        try {
            if(!is_array($encryptInfo)) {
                throw new Exception('is not an array');
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
