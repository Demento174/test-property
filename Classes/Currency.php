<?php

namespace Classes;

class Currency
{
    static $url_request = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=';
    static $currency_id =
        [
            'USD'=>
                [
                    'ID'=>"R01235",
                    "sign"=>"$",
                ],
            'EUR'=>
                [
                    'ID'=>"R01239",
                    "sign"=>"€"
                ],
            'CNY'=>
                [
                    'ID'=>"R01375",
                    "sign"=>"¥"
                ],
            'AED'=>
                [
                    'ID'=>"R01230",
                    "sign"=>""
                ],
        ];
    static $settings =
        [
            'currency'=> ['USD','AED']
        ];
    private string $url;

    private $currency;

    private \SimpleXMLElement $xml;

    public function __construct()
    {

        $this->url = $this->set_url();

        $str = $this->request($this->url);

        $this->xml =$this->set_xml($str);

        $this->currency = $this->set_currency($this->xml);

    }

    private function set_url()
    {

        return self::$url_request.date('d/m/Y');
    }

    private function set_currency(\SimpleXMLElement $xml):array
    {

        $result = [];
        foreach (self::$settings['currency'] as $item)
        {
            $id = self::$currency_id[$item]['ID'];


            $result[$item]=
                [
                    'value'=>mb_substr((float)str_replace(',','.',$xml->xpath('/ValCurs/Valute[@ID="'.$id.'"]')[0]->Value[0]) / $xml->xpath('/ValCurs/Valute[@ID="'.$id.'"]')[0]->Nominal[0],0,5),
                    'sign'=>self::$currency_id[$item]['sign'],
                ];
        }
        return $result;
    }

    private function set_xml(string $stream):\SimpleXMLElement
    {
       return new \SimpleXMLElement($stream);
    }

    private function request(string $url):string
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Accept: application/xml",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }



    public function get_currency():?array
    {

        return $this->currency;
    }

    public function ru(int $price):array
    {
        return
            [
                'value'=>(int)($this->currency['AED']['value']*$price),
                'sign'=>'RU'
            ];
    }

    public function usd(int $price):array
    {
        return
            [
                'value'=>(int)(($this->currency['AED']['value']*$price)/$this->currency['USD']['value']),
                'sign'=>'USD'
            ];
    }
}