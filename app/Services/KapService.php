<?php


namespace App\Services;


use App\Models\Client;
use App\Models\KapRequest;
use App\Models\PreOrderCar;
use SimpleXMLElement;

class KapService
{

    public function get($request)
    {
        $value = $request->value;
        //тип запроса iinbin/vin/grnz
        $type = $request->type;

        $can = false;
        $success = false;
        $message = 'Нет доступа';
        $user = app(AuthService::class)->auth();
        $result = [];
        $kap_request = null;


        if($user->role === 'liner') {
            $preorder_id = $request->preorder_id;
            $kap_request = KapRequest::where('iinbin', $user->idnum)->orderByDesc('created_at')->first();

            if ($preorder_id) {
                $preorder = PreOrderCar::find($preorder_id);
                $client = Client::find($preorder->client_id);
                if ($client) {
                    $message = 'Клиент найден';
                    $kap_request = KapRequest::where('iinbin', $client->idnum)->orderByDesc('created_at')->first();
                }
            }

            if (!$kap_request && ($preorder && $preorder->liner_id === $user->id && ($preorder->status == 0 || $preorder->status == 4))) {
                $can = true;
                $value = $client->idnum;
                $type = 'iinbin';
            }
        }

        if($user->role === 'moderator') {
            $can = true;
        }

        if ($kap_request) {
            $xml = new SimpleXMLElement($kap_request->xml_response);
            $record = $xml->script->dataset->records->record;
            $result['items'] = $this->convertXmlDatToArray($record);
            $result['card'] = $kap_request->k_status;
            $message = 'ТС найден';
        }

        if($can){
            if ( strlen($value) > 4 ){
                if($type == 'grnz') {
                    $result = $this->kap_request_grnz($value);
                }else if($type == 'iinbin') {
                    $result = $this->kap_request_iinbin($value);
                }else{
                    $result = $this->kap_request_vin($value);
                }
            } else {
                $message = 'Не менее 4 символов!';
            }

            if(!empty($result['status']) && $result['status']){
                $message = 'Запрос успешно выполнена!';
                $success = true;
                $kap_request_id = $result['id'];
                $kap_request = KapRequest::find($kap_request_id);
                $xml = new SimpleXMLElement($kap_request->xml_response);
                $record = $xml->script->dataset->records->record;
                $result['items'] = $this->convertXmlDatToArray($record);
                $result['card'] = $kap_request->k_status;
            } else {
               $message = 'Запрос был не успешен!';
            }
        }

        return [
            'success' => $success,
            'message' => $message,
            'data' => $result,
        ];
    }


    /**
     * @throws \Exception
     */
    private function kap_request_vin($vin): array
    {
        $message = '';
        $user = app(AuthService::class)->auth();
        $success_checking = false;
//        $result_data = [];
//        exec('cd /var/www/test && LD_LIBRARY_PATH="/opt/kalkancrypt:/opt/kalkancrypt/lib/engines" php list.php '.$vin, $result_data);
        $result_data = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><document pointcode="151140025060" elapsed="00:00:00.1260527"><initiator><system>РОП</system><user>151140025060</user></initiator><script name="РОП/GetAuto" type="scrx"><parameters><parameter name="GRNZ">B583FKM</parameter></parameters><dataset><fields><field length="10" name="STATUS_DATE" type="String" /><field length="10" name="GRNZ" type="String" /><field length="10" name="PREV_GRNZ" type="String" /><field length="40" name="MODEL" type="String" /><field length="4" name="ISSUE_YEAR" type="String" /><field length="20" name="ENGINE_NO" type="String" /><field length="20" name="CHASSIS_NO" type="String" /><field length="20" name="BODY_NO" type="String" /><field length="7" name="COLOR" type="String" /><field length="50" name="COLOR_NAME" type="String" /><field length="10" name="SRTS" type="String" /><field length="3" name="CATEGORY" type="String" /><field length="6" name="ENGINE_POWER_KWT" type="String" /><field length="6" name="ENGINE_POWER_HP" type="String" /><field length="5" name="ENGINE_VOLUME" type="String" /><field length="6" name="MAX_WEIGHT" type="String" /><field length="6" name="UNLOADED_WEIGHT" type="String" /><field length="1" name="STATUS" type="String" /><field length="1" name="OWNER_TYPE" type="String" /><field length="6" name="VEHICLE_TYPE_CODE" type="String" /><field length="20" name="VIN" type="String" /><field length="10" name="PREV_SRTS" type="String" /><field length="1024" name="NOTES" type="String" /><field length="49" name="UNREG_REASON" type="String" /><field length="6" name="UNREG_INCOMING_NUMBER" type="String" /><field length="10" name="FIRST_REG_DATE" type="String" /></fields><records count="1"><record><field name="STATUS_DATE">1999-07-29</field><field name="GRNZ">B583FKM</field><field name="PREV_GRNZ" /><field name="MODEL">ВАЗ 21043</field><field name="ISSUE_YEAR">1999</field><field name="ENGINE_NO">5466318</field><field name="CHASSIS_NO">0681192</field><field name="BODY_NO">ХТА210430Х0711258</field><field name="COLOR">0I00000</field><field name="COLOR_NAME">БЕЛЫЙ</field><field name="SRTS">BH00001885</field><field name="CATEGORY">B</field><field name="ENGINE_POWER_KWT">53</field><field name="ENGINE_POWER_HP">75</field><field name="ENGINE_VOLUME">1400</field><field name="MAX_WEIGHT">1510</field><field name="UNLOADED_WEIGHT">1055</field><field name="STATUS">S</field><field name="OWNER_TYPE">2</field><field name="VEHICLE_TYPE_CODE" /><field name="VIN">ХТА210430Х0711258</field><field name="PREV_SRTS" /><field name="NOTES">МАГ ГН СДАНЫ ТР-Т НЕ ВЫДАН</field><field name="UNREG_REASON">ком. маг</field><field name="UNREG_INCOMING_NUMBER">3306</field><field name="FIRST_REG_DATE" /></record></records></dataset></script><ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#"><ds:SignedInfo><ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" /><ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#gost34310-gost34311" /><ds:Reference URI=""><ds:Transforms><ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature" /><ds:Transform Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315#WithComments" /></ds:Transforms><ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#gost34311" /><ds:DigestValue>gGWvTQOBY4PyzD002/vNFsHApFyB1XL1O5NrnU5g3Wg=</ds:DigestValue></ds:Reference></ds:SignedInfo><ds:SignatureValue>/84Prz+k6ZWCcdNMl/w72mMvvEEBs6TZJEH3FqCboumkA/Q98BxBwRN04+r00yQRiSgmlveO+SLcRAIPXWUALA==</ds:SignatureValue><ds:KeyInfo><ds:X509Data><ds:X509Certificate>MIIEtzCCBGGgAwIBAgIUMFkoybqwMf+yqKN1x91tx0OjFDswDQYJKoMOAwoBAQECBQAwUzELMAkGA1UEBhMCS1oxRDBCBgNVBAMMO9Kw0JvQotCi0KvSmiDQmtCj05jQm9CQ0J3QlNCr0KDQo9Co0Ksg0J7QoNCi0JDQm9Cr0pogKEdPU1QpMB4XDTIwMDkxNTA2MjU0MFoXDTIxMDkxNTA2MjU0MFowggFhMSIwIAYDVQQDDBnQotCj0KHQo9Cf0J7QkiDQodCV0KDQmNCaMRcwFQYDVQQEDA7QotCj0KHQo9Cf0J7QkjEYMBYGA1UEBRMPSUlOODAwNzA3MzAxMjk3MQswCQYDVQQGEwJLWjGBnTCBmgYDVQQKDIGS0JPQntCh0KPQlNCQ0KDQodCi0JLQldCd0J3QntCVINCj0KfQoNCV0JbQlNCV0J3QmNCVICLQnNCY0J3QmNCh0KLQldCg0KHQotCS0J4g0JLQndCj0KLQoNCV0J3QndCY0KUg0JTQldCbINCg0JXQodCf0KPQkdCb0JjQmtCYINCa0JDQl9CQ0KXQodCi0JDQnSIxGDAWBgNVBAsMD0JJTjk2MDM0MDAwMDUxNDEdMBsGA1UEKgwU0JDQnNCQ0J3QotCQ0JXQktCY0KcxIjAgBgkqhkiG9w0BCQEWE0dPU1pBQ1VQTVZEQE1BSUwuS1owbDAlBgkqgw4DCgEBAQEwGAYKKoMOAwoBAQEBAQYKKoMOAwoBAwEBAANDAARAhbF4QiK1HRKhKCI84aLFKJAcmfz7lRdAraxOs1VPKe1naHCqEfCbZOM1+dYY+50pmT+jhy1J5OWTUs6+loJWhaOCAeswggHnMA4GA1UdDwEB/wQEAwIGwDAoBgNVHSUEITAfBggrBgEFBQcDBAYIKoMOAwMEAQIGCSqDDgMDBAECAjAPBgNVHSMECDAGgARbanPpMB0GA1UdDgQWBBQ3dvUIx68oGznprAGdmuoYncfVHjBeBgNVHSAEVzBVMFMGByqDDgMDAgEwSDAhBggrBgEFBQcCARYVaHR0cDovL3BraS5nb3Yua3ovY3BzMCMGCCsGAQUFBwICMBcMFWh0dHA6Ly9wa2kuZ292Lmt6L2NwczBYBgNVHR8EUTBPME2gS6BJhiJodHRwOi8vY3JsLnBraS5nb3Yua3ovbmNhX2dvc3QuY3JshiNodHRwOi8vY3JsMS5wa2kuZ292Lmt6L25jYV9nb3N0LmNybDBcBgNVHS4EVTBTMFGgT6BNhiRodHRwOi8vY3JsLnBraS5nb3Yua3ovbmNhX2RfZ29zdC5jcmyGJWh0dHA6Ly9jcmwxLnBraS5nb3Yua3ovbmNhX2RfZ29zdC5jcmwwYwYIKwYBBQUHAQEEVzBVMC8GCCsGAQUFBzAChiNodHRwOi8vcGtpLmdvdi5rei9jZXJ0L25jYV9nb3N0LmNlcjAiBggrBgEFBQcwAYYWaHR0cDovL29jc3AucGtpLmdvdi5rejANBgkqgw4DCgEBAQIFAANBAHqwPfQHK+nBXPcQbqe516q/QGdCPa2GxWzvL3tm1PzAEp5TN5QicJq4+i5pJ2yAiZCMHGgE6Dk2Qe3syUCZcS8=</ds:X509Certificate></ds:X509Data></ds:KeyInfo></ds:Signature></document>';

        if( $this->__isValidXml($result_data) ){
            $xml = new SimpleXMLElement( $result_data );
            if ($xml->error){
                $message = "Ошибка запроса: ". $xml->error->message;
            } elseif ( count($xml->script->dataset->records->record) >= 1 ){
                $record = $xml->script->dataset->records->record;
                $message = $this->checkCondition($record);
                $success_checking = true;
            } elseif ( count($xml->script->dataset->records->record) == 0 ){
                $message = 'Нет записей в АИС КАП по данному номеру кузова (код V001)';
            }
        }

        if ($success_checking) {
            $kap_request = new KapRequest;
            $kap_request->vin = $vin;
            $kap_request->k_status = $message;
            $kap_request->xml_response = $result_data;
            $kap_request->user_id = $user->id;
            $kap_request->created_at = time();
            $kap_request->save();
            if (!empty($kap_request->id)) {
                return [
                    'id' => $kap_request->id,
                    'status' => true,
                    'message' => $message
                ];
            }
        }

        return [
            'status' => false
        ];
    }

    private function kap_request_iinbin($iinbin): array
    {
        $message = '';
        $user = app(AuthService::class)->auth();
        $success_checking = false;
//        $result_data = [];
//        exec('cd /var/www/test && LD_LIBRARY_PATH="/opt/kalkancrypt:/opt/kalkancrypt/lib/engines" php list.php '.$iinbin, $result_data);
        $result_data = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><document pointcode="151140025060" elapsed="00:00:00.1260527"><initiator><system>РОП</system><user>151140025060</user></initiator><script name="РОП/GetAuto" type="scrx"><parameters><parameter name="GRNZ">B583FKM</parameter></parameters><dataset><fields><field length="10" name="STATUS_DATE" type="String" /><field length="10" name="GRNZ" type="String" /><field length="10" name="PREV_GRNZ" type="String" /><field length="40" name="MODEL" type="String" /><field length="4" name="ISSUE_YEAR" type="String" /><field length="20" name="ENGINE_NO" type="String" /><field length="20" name="CHASSIS_NO" type="String" /><field length="20" name="BODY_NO" type="String" /><field length="7" name="COLOR" type="String" /><field length="50" name="COLOR_NAME" type="String" /><field length="10" name="SRTS" type="String" /><field length="3" name="CATEGORY" type="String" /><field length="6" name="ENGINE_POWER_KWT" type="String" /><field length="6" name="ENGINE_POWER_HP" type="String" /><field length="5" name="ENGINE_VOLUME" type="String" /><field length="6" name="MAX_WEIGHT" type="String" /><field length="6" name="UNLOADED_WEIGHT" type="String" /><field length="1" name="STATUS" type="String" /><field length="1" name="OWNER_TYPE" type="String" /><field length="6" name="VEHICLE_TYPE_CODE" type="String" /><field length="20" name="VIN" type="String" /><field length="10" name="PREV_SRTS" type="String" /><field length="1024" name="NOTES" type="String" /><field length="49" name="UNREG_REASON" type="String" /><field length="6" name="UNREG_INCOMING_NUMBER" type="String" /><field length="10" name="FIRST_REG_DATE" type="String" /></fields><records count="1"><record><field name="STATUS_DATE">1999-07-29</field><field name="GRNZ">B583FKM</field><field name="PREV_GRNZ" /><field name="MODEL">ВАЗ 21043</field><field name="ISSUE_YEAR">1999</field><field name="ENGINE_NO">5466318</field><field name="CHASSIS_NO">0681192</field><field name="BODY_NO">ХТА210430Х0711258</field><field name="COLOR">0I00000</field><field name="COLOR_NAME">БЕЛЫЙ</field><field name="SRTS">BH00001885</field><field name="CATEGORY">B</field><field name="ENGINE_POWER_KWT">53</field><field name="ENGINE_POWER_HP">75</field><field name="ENGINE_VOLUME">1400</field><field name="MAX_WEIGHT">1510</field><field name="UNLOADED_WEIGHT">1055</field><field name="STATUS">S</field><field name="OWNER_TYPE">2</field><field name="VEHICLE_TYPE_CODE" /><field name="VIN">ХТА210430Х0711258</field><field name="PREV_SRTS" /><field name="NOTES">МАГ ГН СДАНЫ ТР-Т НЕ ВЫДАН</field><field name="UNREG_REASON">ком. маг</field><field name="UNREG_INCOMING_NUMBER">3306</field><field name="FIRST_REG_DATE" /></record></records></dataset></script><ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#"><ds:SignedInfo><ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" /><ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#gost34310-gost34311" /><ds:Reference URI=""><ds:Transforms><ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature" /><ds:Transform Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315#WithComments" /></ds:Transforms><ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#gost34311" /><ds:DigestValue>gGWvTQOBY4PyzD002/vNFsHApFyB1XL1O5NrnU5g3Wg=</ds:DigestValue></ds:Reference></ds:SignedInfo><ds:SignatureValue>/84Prz+k6ZWCcdNMl/w72mMvvEEBs6TZJEH3FqCboumkA/Q98BxBwRN04+r00yQRiSgmlveO+SLcRAIPXWUALA==</ds:SignatureValue><ds:KeyInfo><ds:X509Data><ds:X509Certificate>MIIEtzCCBGGgAwIBAgIUMFkoybqwMf+yqKN1x91tx0OjFDswDQYJKoMOAwoBAQECBQAwUzELMAkGA1UEBhMCS1oxRDBCBgNVBAMMO9Kw0JvQotCi0KvSmiDQmtCj05jQm9CQ0J3QlNCr0KDQo9Co0Ksg0J7QoNCi0JDQm9Cr0pogKEdPU1QpMB4XDTIwMDkxNTA2MjU0MFoXDTIxMDkxNTA2MjU0MFowggFhMSIwIAYDVQQDDBnQotCj0KHQo9Cf0J7QkiDQodCV0KDQmNCaMRcwFQYDVQQEDA7QotCj0KHQo9Cf0J7QkjEYMBYGA1UEBRMPSUlOODAwNzA3MzAxMjk3MQswCQYDVQQGEwJLWjGBnTCBmgYDVQQKDIGS0JPQntCh0KPQlNCQ0KDQodCi0JLQldCd0J3QntCVINCj0KfQoNCV0JbQlNCV0J3QmNCVICLQnNCY0J3QmNCh0KLQldCg0KHQotCS0J4g0JLQndCj0KLQoNCV0J3QndCY0KUg0JTQldCbINCg0JXQodCf0KPQkdCb0JjQmtCYINCa0JDQl9CQ0KXQodCi0JDQnSIxGDAWBgNVBAsMD0JJTjk2MDM0MDAwMDUxNDEdMBsGA1UEKgwU0JDQnNCQ0J3QotCQ0JXQktCY0KcxIjAgBgkqhkiG9w0BCQEWE0dPU1pBQ1VQTVZEQE1BSUwuS1owbDAlBgkqgw4DCgEBAQEwGAYKKoMOAwoBAQEBAQYKKoMOAwoBAwEBAANDAARAhbF4QiK1HRKhKCI84aLFKJAcmfz7lRdAraxOs1VPKe1naHCqEfCbZOM1+dYY+50pmT+jhy1J5OWTUs6+loJWhaOCAeswggHnMA4GA1UdDwEB/wQEAwIGwDAoBgNVHSUEITAfBggrBgEFBQcDBAYIKoMOAwMEAQIGCSqDDgMDBAECAjAPBgNVHSMECDAGgARbanPpMB0GA1UdDgQWBBQ3dvUIx68oGznprAGdmuoYncfVHjBeBgNVHSAEVzBVMFMGByqDDgMDAgEwSDAhBggrBgEFBQcCARYVaHR0cDovL3BraS5nb3Yua3ovY3BzMCMGCCsGAQUFBwICMBcMFWh0dHA6Ly9wa2kuZ292Lmt6L2NwczBYBgNVHR8EUTBPME2gS6BJhiJodHRwOi8vY3JsLnBraS5nb3Yua3ovbmNhX2dvc3QuY3JshiNodHRwOi8vY3JsMS5wa2kuZ292Lmt6L25jYV9nb3N0LmNybDBcBgNVHS4EVTBTMFGgT6BNhiRodHRwOi8vY3JsLnBraS5nb3Yua3ovbmNhX2RfZ29zdC5jcmyGJWh0dHA6Ly9jcmwxLnBraS5nb3Yua3ovbmNhX2RfZ29zdC5jcmwwYwYIKwYBBQUHAQEEVzBVMC8GCCsGAQUFBzAChiNodHRwOi8vcGtpLmdvdi5rei9jZXJ0L25jYV9nb3N0LmNlcjAiBggrBgEFBQcwAYYWaHR0cDovL29jc3AucGtpLmdvdi5rejANBgkqgw4DCgEBAQIFAANBAHqwPfQHK+nBXPcQbqe516q/QGdCPa2GxWzvL3tm1PzAEp5TN5QicJq4+i5pJ2yAiZCMHGgE6Dk2Qe3syUCZcS8=</ds:X509Certificate></ds:X509Data></ds:KeyInfo></ds:Signature></document>';

        if( $this->__isValidXml($result_data) ){
            $xml = new SimpleXMLElement( $result_data );
            if ($xml->error){
                $message = "Ошибка запроса: ". $xml->error->message;
            } elseif ( count($xml->script->dataset->records->record) >= 1 ){
                $record = $xml->script->dataset->records->record;
                $message = $this->checkCondition($record);
                $success_checking = true;
            } elseif ( count($xml->script->dataset->records->record) == 0 ){
                $message = 'Нет записей в АИС КАП по данному номеру кузова (код V001)';
            }
        }

        if ($success_checking) {
            $kap_request = new KapRequest;
            $kap_request->iinbin = $iinbin;
            $kap_request->k_status = $message;
            $kap_request->xml_response = $result_data;
            $kap_request->user_id = $user->id;
            $kap_request->created_at = time();
            $kap_request->save();
            if (!empty($kap_request->id)) {
                return [
                    'id' => $kap_request->id,
                    'status' => true,
                    'message' => $message
                ];
            }
        }

        return [
            'status' => false
        ];
    }

    private function kap_request_grnz($grnz): array
    {
        $message = '';
        $user = app(AuthService::class)->auth();
        $success_checking = false;
//        $result_data = [];
//        exec('cd /var/www/test && LD_LIBRARY_PATH="/opt/kalkancrypt:/opt/kalkancrypt/lib/engines" php grnz_list.php '.$grnz, $result_data);
        $result_data = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><document pointcode="151140025060" elapsed="00:00:00.1260527"><initiator><system>РОП</system><user>151140025060</user></initiator><script name="РОП/GetAuto" type="scrx"><parameters><parameter name="GRNZ">B583FKM</parameter></parameters><dataset><fields><field length="10" name="STATUS_DATE" type="String" /><field length="10" name="GRNZ" type="String" /><field length="10" name="PREV_GRNZ" type="String" /><field length="40" name="MODEL" type="String" /><field length="4" name="ISSUE_YEAR" type="String" /><field length="20" name="ENGINE_NO" type="String" /><field length="20" name="CHASSIS_NO" type="String" /><field length="20" name="BODY_NO" type="String" /><field length="7" name="COLOR" type="String" /><field length="50" name="COLOR_NAME" type="String" /><field length="10" name="SRTS" type="String" /><field length="3" name="CATEGORY" type="String" /><field length="6" name="ENGINE_POWER_KWT" type="String" /><field length="6" name="ENGINE_POWER_HP" type="String" /><field length="5" name="ENGINE_VOLUME" type="String" /><field length="6" name="MAX_WEIGHT" type="String" /><field length="6" name="UNLOADED_WEIGHT" type="String" /><field length="1" name="STATUS" type="String" /><field length="1" name="OWNER_TYPE" type="String" /><field length="6" name="VEHICLE_TYPE_CODE" type="String" /><field length="20" name="VIN" type="String" /><field length="10" name="PREV_SRTS" type="String" /><field length="1024" name="NOTES" type="String" /><field length="49" name="UNREG_REASON" type="String" /><field length="6" name="UNREG_INCOMING_NUMBER" type="String" /><field length="10" name="FIRST_REG_DATE" type="String" /></fields><records count="1"><record><field name="STATUS_DATE">1999-07-29</field><field name="GRNZ">B583FKM</field><field name="PREV_GRNZ" /><field name="MODEL">ВАЗ 21043</field><field name="ISSUE_YEAR">1999</field><field name="ENGINE_NO">5466318</field><field name="CHASSIS_NO">0681192</field><field name="BODY_NO">ХТА210430Х0711258</field><field name="COLOR">0I00000</field><field name="COLOR_NAME">БЕЛЫЙ</field><field name="SRTS">BH00001885</field><field name="CATEGORY">B</field><field name="ENGINE_POWER_KWT">53</field><field name="ENGINE_POWER_HP">75</field><field name="ENGINE_VOLUME">1400</field><field name="MAX_WEIGHT">1510</field><field name="UNLOADED_WEIGHT">1055</field><field name="STATUS">S</field><field name="OWNER_TYPE">2</field><field name="VEHICLE_TYPE_CODE" /><field name="VIN">ХТА210430Х0711258</field><field name="PREV_SRTS" /><field name="NOTES">МАГ ГН СДАНЫ ТР-Т НЕ ВЫДАН</field><field name="UNREG_REASON">ком. маг</field><field name="UNREG_INCOMING_NUMBER">3306</field><field name="FIRST_REG_DATE" /></record></records></dataset></script><ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#"><ds:SignedInfo><ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" /><ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#gost34310-gost34311" /><ds:Reference URI=""><ds:Transforms><ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature" /><ds:Transform Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315#WithComments" /></ds:Transforms><ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#gost34311" /><ds:DigestValue>gGWvTQOBY4PyzD002/vNFsHApFyB1XL1O5NrnU5g3Wg=</ds:DigestValue></ds:Reference></ds:SignedInfo><ds:SignatureValue>/84Prz+k6ZWCcdNMl/w72mMvvEEBs6TZJEH3FqCboumkA/Q98BxBwRN04+r00yQRiSgmlveO+SLcRAIPXWUALA==</ds:SignatureValue><ds:KeyInfo><ds:X509Data><ds:X509Certificate>MIIEtzCCBGGgAwIBAgIUMFkoybqwMf+yqKN1x91tx0OjFDswDQYJKoMOAwoBAQECBQAwUzELMAkGA1UEBhMCS1oxRDBCBgNVBAMMO9Kw0JvQotCi0KvSmiDQmtCj05jQm9CQ0J3QlNCr0KDQo9Co0Ksg0J7QoNCi0JDQm9Cr0pogKEdPU1QpMB4XDTIwMDkxNTA2MjU0MFoXDTIxMDkxNTA2MjU0MFowggFhMSIwIAYDVQQDDBnQotCj0KHQo9Cf0J7QkiDQodCV0KDQmNCaMRcwFQYDVQQEDA7QotCj0KHQo9Cf0J7QkjEYMBYGA1UEBRMPSUlOODAwNzA3MzAxMjk3MQswCQYDVQQGEwJLWjGBnTCBmgYDVQQKDIGS0JPQntCh0KPQlNCQ0KDQodCi0JLQldCd0J3QntCVINCj0KfQoNCV0JbQlNCV0J3QmNCVICLQnNCY0J3QmNCh0KLQldCg0KHQotCS0J4g0JLQndCj0KLQoNCV0J3QndCY0KUg0JTQldCbINCg0JXQodCf0KPQkdCb0JjQmtCYINCa0JDQl9CQ0KXQodCi0JDQnSIxGDAWBgNVBAsMD0JJTjk2MDM0MDAwMDUxNDEdMBsGA1UEKgwU0JDQnNCQ0J3QotCQ0JXQktCY0KcxIjAgBgkqhkiG9w0BCQEWE0dPU1pBQ1VQTVZEQE1BSUwuS1owbDAlBgkqgw4DCgEBAQEwGAYKKoMOAwoBAQEBAQYKKoMOAwoBAwEBAANDAARAhbF4QiK1HRKhKCI84aLFKJAcmfz7lRdAraxOs1VPKe1naHCqEfCbZOM1+dYY+50pmT+jhy1J5OWTUs6+loJWhaOCAeswggHnMA4GA1UdDwEB/wQEAwIGwDAoBgNVHSUEITAfBggrBgEFBQcDBAYIKoMOAwMEAQIGCSqDDgMDBAECAjAPBgNVHSMECDAGgARbanPpMB0GA1UdDgQWBBQ3dvUIx68oGznprAGdmuoYncfVHjBeBgNVHSAEVzBVMFMGByqDDgMDAgEwSDAhBggrBgEFBQcCARYVaHR0cDovL3BraS5nb3Yua3ovY3BzMCMGCCsGAQUFBwICMBcMFWh0dHA6Ly9wa2kuZ292Lmt6L2NwczBYBgNVHR8EUTBPME2gS6BJhiJodHRwOi8vY3JsLnBraS5nb3Yua3ovbmNhX2dvc3QuY3JshiNodHRwOi8vY3JsMS5wa2kuZ292Lmt6L25jYV9nb3N0LmNybDBcBgNVHS4EVTBTMFGgT6BNhiRodHRwOi8vY3JsLnBraS5nb3Yua3ovbmNhX2RfZ29zdC5jcmyGJWh0dHA6Ly9jcmwxLnBraS5nb3Yua3ovbmNhX2RfZ29zdC5jcmwwYwYIKwYBBQUHAQEEVzBVMC8GCCsGAQUFBzAChiNodHRwOi8vcGtpLmdvdi5rei9jZXJ0L25jYV9nb3N0LmNlcjAiBggrBgEFBQcwAYYWaHR0cDovL29jc3AucGtpLmdvdi5rejANBgkqgw4DCgEBAQIFAANBAHqwPfQHK+nBXPcQbqe516q/QGdCPa2GxWzvL3tm1PzAEp5TN5QicJq4+i5pJ2yAiZCMHGgE6Dk2Qe3syUCZcS8=</ds:X509Certificate></ds:X509Data></ds:KeyInfo></ds:Signature></document>';

        if( $this->__isValidXml($result_data) ){
            $xml = new SimpleXMLElement( $result_data );
            if ($xml->error){
                $message = "Ошибка запроса: ". $xml->error->message;
            } elseif ( count($xml->script->dataset->records->record) >= 1 ){
                $record = $xml->script->dataset->records->record;
                $message = $this->checkCondition($record);
                $success_checking = true;
            } elseif ( count($xml->script->dataset->records->record) == 0 ){
                $message = 'Нет записей в АИС КАП по данному ГРНЗ (код V001)';
            }
        }

        if ($success_checking) {
            $kap_request = new KapRequest;
            $kap_request->grnz = $grnz;
            $kap_request->k_status = $message;
            $kap_request->xml_response = $result_data;
            $kap_request->user_id = $user->id;
            $kap_request->created_at = time();
            $kap_request->save();
            if (!empty($kap_request->id)) {
                return [
                    'id' => $kap_request->id,
                    'status' => true,
                    'message' => $message
                ];
            }
        }

        return [
            'status' => false
        ];
    }

    private function checkCondition($record){

        $data_type = [
            "GRNZ" => "ГРНЗ",
            "STATUS_DATE" => "дата операции",
            "MODEL" => "марка, модель, модификация ТС",
            "ISSUE_YEAR" => "год выпуска ТС",
            "ENGINE_NO" => "номер двигателя ТС",
            "CHASSIS_NO" => "номер шасси ТС",
            "BODY_NO" => "номер кузова ТС (Vin-код)",
            "COLOR_NAME" => "цвет",
            "CATEGORY" => "категория ТС",
            "ENGINE_VOLUME" => "объем двигателя (куб. см)",
            "MAX_WEIGHT" => "разрешенная максимальная масса",
            "UNLOADED_WEIGHT" => "масса без нагрузки",
            "STATUS" => "статус карточки",
            "VIN" => "VIN ТС",
            "UNREG_REASON" => "причина снятия с учета ТС",
            "FIRSTNAME" => "Имя",
            "LASTNAME" => "Фамилия",
            "MIDNAME" => "Отчество",
            "IINBIN" => "ИИН/БИН"
        ];

        $state = '';
        $allow_set_state_by_last = true;
        $sorted_array = [];

        $state .= '';
        foreach( $record->field as $field ){
            $field_name = $field->attributes();
            $field_name = $field_name[0];

            if ( $field_name == "STATUS" && isset($data_type[strval($field_name)]) && $field != 'S' ){
                $state = 'Проверка VIN, номер кузова не снят с учета (V004)';
                break;
            }

            if( isset($data_type[strval($field_name)]) ){
                $field = $field != '' ? $field : 'Нет записи';
                $state .=  ' <span><b>'. $data_type[strval($field_name)].':</b> ';

                $state .= ' '.$field.'</span>; ';
            }
        }

        return $state;
    }


    private function convertXmlDatToArray($records): array
    {

        $data_type = [
            "GRNZ" => "ГРНЗ",
            "STATUS_DATE" => "дата операции",
            "MODEL" => "марка, модель, модификация ТС",
            "ISSUE_YEAR" => "год выпуска ТС",
            "ENGINE_NO" => "номер двигателя ТС",
            "CHASSIS_NO" => "номер шасси ТС",
            "BODY_NO" => "номер кузова ТС (Vin-код)",
            "COLOR_NAME" => "цвет",
            "CATEGORY" => "категория ТС",
            "ENGINE_VOLUME" => "объем двигателя (куб. см)",
            "MAX_WEIGHT" => "разрешенная максимальная масса",
            "UNLOADED_WEIGHT" => "масса без нагрузки",
            "STATUS" => "статус карточки",
            "VIN" => "VIN ТС",
            "UNREG_REASON" => "причина снятия с учета ТС",
            "FIRSTNAME" => "Имя",
            "LASTNAME" => "Фамилия",
            "MIDNAME" => "Отчество",
            "IINBIN" => "ИИН/БИН"
        ];
        $k = 0;
        foreach ($records as $record) {
            $k++;
            $list = [];
            foreach ($record as $field) {
                $field_name = $field->attributes();
                $field_name = $field_name[0];
                $status = '';
                $item_arr = [];
                if (isset($data_type[strval($field_name)])) {
                    if ($field_name == "STATUS") {
                        switch ($field) {
                            case "P":
                                $status = 'карточка распечатана (' . $field . ')';
                                break;
                            case "S":
                                $status_id = $field;
                                $status = 'Карточка снята с учета (' . $field . ')';
                                break;
                            case "B":
                                $status = 'Карточка распечатана, временный ввоз (' . $field . ')';
                                break;
                            case "U":
                                $status = 'Карточка утверждена (' . $field . ')';
                                break;
                            case "N":
                                $status = 'Новая карточка (' . $field . ')';
                                break;
                            case "V":
                                $status = 'Карточка на временном учете (' . $field . ')';
                                break;
                            default:
                                $status = $field;
                                break;
                        }
                        $field[0] = $status;
                    }
                    $string = json_encode($field);
                    $list[] = json_decode($string, true);
                }
            }
            $res[] = $list;
        }

        $newArr = [];
        $ids = [];
        foreach ($res as $key => $listItem){
            $newItemArr = [];
            foreach ($listItem as $value){
                if(count($value) > 1) {
                    if($value[0] === 'Карточка снята с учета (S)'){
                        $ids[] = $key+1;
                    }
                    $newItemArr[strtolower($value['@attributes']['name'])] = $value[0];
                }else{
                    $newItemArr[strtolower($value['@attributes']['name'])] = '-';
                }
                $newItemArr['id'] = $key+1;
            }
            $newArr[] = $newItemArr;
        }

        $arr = [];
        foreach ($newArr as $item){
            if(in_array($item['id'], $ids)){
                $arr[] = $item;
            }
        }
        return $arr;
    }

    private function __isValidXml($content)
    {
        $content = trim($content);
        if (empty($content)) {
            return false;
        }
        //html go to hell!
        if (stripos($content, '<!DOCTYPE html>') !== false) {
            return false;
        }

        libxml_use_internal_errors(true);
        simplexml_load_string($content);
        $errors = libxml_get_errors();
        libxml_clear_errors();

        return empty($errors);
    }


}
