<?php

use Zend\Http\Client;
/**
 *
 */
class Melhorcotacao_Envio_Model_Resource extends Mage_Core_Model_Resource_Db_Abstract
{


    public function load($printQuery = false, $logQuery = false) {

    $client = new Zend_Http_Client();
    $options = ['adapter'=>'Zend\Http\Client\Adapter\Curl',
                'curloptions'  => [CURLOPT_FOLLOWLOCATION => true],
                'timeout' => 30
    ];

    $client->setUri("https://api.melhorenvio.com.br/v1/shipping");
    $client->setMethod('POST');
    $client->setHeaders(['Content-Type'=>'application/json;charset=UTF-8']);


    $content = file_get_contents(__DIR__."/../Cadastraconfig.json");
    $file_decoded = json_decode($content);

    $api_key = $file_decoded->api_key;
    $secret_token = $file_decoded->secret_token;
    $email = $file_decoded->email ;

    $client->setAuth($api_key,$secret_token);

    $rqst = array('email' => $email);

    $client->setParameterPost($rqst);

    //var_dump($client->request()->getBody());

    return $client->request()->getBody();
   }
}


?>
