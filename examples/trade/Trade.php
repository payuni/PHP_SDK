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
class Trade
{
    public function __construct(Request $request)
    {
        $this->merKey = '12345678901234567890123456789012';
        $this->merIV  = '1234567890123456';
        $this->payuniApi = new \Payuni\Sdk\PayuniApi($this->merKey, $this->merIV);
    }
    /**
     * trade query sample code
     * @author   presco.
     * @datetime 2022/08/29
     *
     */
    public function tradeQuery(){
        $encryptInfo = [
            'MerID' => 'abc',
            'MerTradeNo' => 'test20220829111528',
            'Timestamp' => time(),
            ...
        ];
        $result = $this->payuniApi->UniversalTrade($encryptInfo, 'trade_query');
    }
    /**
     * treade close sample code
     * @author   presco.
     * @datetime 2022/08/29
     *
     */
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
    /**
     * trade cancel sample code
     * @author   presco.
     * @datetime 2022/08/29
     *
     */
    public function tradeCancel(){
        $encryptInfo = [
            'MerID' => 'abc',
            'TradeNo' => '16614190477810373246',
            'Timestamp' => time()
        ];
        $result = $this->payuniApi->UniversalTrade($encryptInfo, 'trade_cancel');
    }
    /**
     * trade refund icash sample code
     * @author   presco.
     * @datetime 2022/10/12
     *
     */
    public function tradeRefundIcash(){
        $encryptInfo = [
            'MerID' => 'abc',
            'TradeNo' => '1665472985627866043',
            'TradeAmt' => 100,
            'Timestamp' => time()
        ];
        $result = $this->payuniApi->UniversalTrade($encryptInfo, 'trade_refund_icash');
    }
}
