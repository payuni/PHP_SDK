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
namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(Request $request)
    {
        $this->merKey = '12345678901234567890123456789012';
        $this->merIV  = '1234567890123456';
        $this->payuniApi = new \Payuni\Sdk\PayuniApi($this->merKey, $this->merIV);
    }
    /**
     * upp sample code
     * @author   presco.
     * @datetime 2022/08/29
     *
     */
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
    /**
     * returnURL sample code
     * @author   presco.
     * @datetime 2022/08/29
     *
     */
    public function return(Request $request){
        $postData = $request->all();
        $result = $this->payuniApi->ResultProcess($postData);
    }
    /**
     * notifyURL sample code
     * @author   presco.
     * @datetime 2022/08/29
     *
     */
    public function notify(Request $request){
        $postData = $request->all();
        $result = $this->payuniApi->ResultProcess($postData);
    }
    /**
     * credit sample code
     * @author   presco.
     * @datetime 2022/08/29
     *
     */
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
    /**
     * atm sample code
     * @author   presco.
     * @datetime 2022/08/29
     *
     */
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
    /**
     * cvs sample code
     * @author   presco.
     * @datetime 2022/08/29
     *
     */
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
}
