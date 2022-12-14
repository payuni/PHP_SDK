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

class CardBind
{
    public function __construct(Request $request)
    {
        $this->merKey = '12345678901234567890123456789012';
        $this->merIV  = '1234567890123456';
        $this->payuniApi = new \Payuni\Sdk\PayuniApi($this->merKey, $this->merIV);
    }
    /**
     * trade bind query sample code
     * @author   presco.
     * @datetime 2022/08/29
     *
     */
    public function tradeBindQuery(){
        $encryptInfo = [
            'MerID' => 'abc',
            'Timestamp' => time(),
            ...
        ];
        $result = $this->payuniApi->UniversalTrade($encryptInfo, 'trade_bind_query');
    }
    /**
     * treade bind cancel sample code
     * @author   presco.
     * @datetime 2022/08/29
     *
     */
    public function tradeBindCancel(){
        $encryptInfo = [
            'MerID' => 'abc',
            'Timestamp' => time(),
            'UseTokenType' => 1,
            'BindVal' => '...',
            ...
        ];
        $result = $this->payuniApi->UniversalTrade($encryptInfo, 'trade_bind_cancel');
    }
}
