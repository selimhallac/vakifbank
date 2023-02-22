<?php

namespace Phpdev;

Class Vakifbank
{
    
    
    public $username = "";
    public $pasword = "";
    public $customerno = "";
    
    function __construct($username, $password, $customer_no)
    {
        $this->username       = $username;
        $this->password       = $password;
        $this->customerno     = $customer_no;
    }
    
    
    
    public function hesap_hareketleri($tarih1, $tarih2)
    {
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://vbservice.vakifbank.com.tr/HesapHareketleri.OnlineEkstre/SOnlineEkstreServis.svc',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:peak="Peak.Integration.ExternalInbound.Ekstre" xmlns:peak1="http://schemas.datacontract.org/2004/07/Peak.Integration.ExternalInbound.Ekstre.DataTransferObjects">
        <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing"><wsa:Action>Peak.Integration.ExternalInbound.Ekstre/ISOnlineEkstreServis/GetirHareket</wsa:Action><wsa:To>https://vbservice.vakifbank.com.tr/HesapHareketleri.OnlineEkstre/SOnlineEkstreServis.svc</wsa:To></soap:Header>
        <soap:Body>
            <peak:GetirHareket>
                <!--Optional:-->
                <peak:sorgu>
                    <peak1:MusteriNo>'.$this->customerno.'</peak1:MusteriNo>
                    <peak1:KurumKullanici>'.$this->username.'</peak1:KurumKullanici>
                    <peak1:Sifre>'.$this->password.'</peak1:Sifre>
                    <peak1:SorguBaslangicTarihi>'.$tarih1.'</peak1:SorguBaslangicTarihi>
                    <peak1:SorguBitisTarihi>'.$tarih2.'</peak1:SorguBitisTarihi>
                    <!--Optional:-->
                    <peak1:HesapNo></peak1:HesapNo>
                    <!--Optional:-->
                    <peak1:HareketTipi xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
                    <!--Optional:-->
                    <!--Optional:-->
                </peak:sorgu>
            </peak:GetirHareket>
        </soap:Body>
        </soap:Envelope>',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/soap+xml;'
        ),
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $result = str_replace('<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing"><s:Header><a:Action s:mustUnderstand="1">Peak.Integration.ExternalInbound.Ekstre/ISOnlineEkstreServis/GetirHareketResponse</a:Action></s:Header><s:Body><GetirHareketResponse xmlns="Peak.Integration.ExternalInbound.Ekstre">', '', $result);
        $result = str_replace('</GetirHareketResponse></s:Body></s:Envelope>', '', $result);
        $result = str_replace('xmlns="Peak.Integration.ExternalInbound.Ekstre"', '', $result);
        $result = str_replace('xmlns:b="http://schemas.datacontract.org/2004/07/Peak.Integration.ExternalInbound.Ekstre.DataTransferObjects" xmlns:i="http://www.w3.org/2001/XMLSchema-instance"', '', $result);
        $result = str_replace('b:', '', $result);
        $xmlobject = @simplexml_load_string($result);

        if(isset($xmlobject->Hesaplar->DtoEkstreHesap)){
            return json_encode([
                'statu'=>true,
                'response' =>$xmlobject
            ]);
        } else {
            return json_encode([
                'statu'=>false,
                'response' => (string) $xmlobject->IslemAciklamasi
            ]);
        }
    }
    
    
}