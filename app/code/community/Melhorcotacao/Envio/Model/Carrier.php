<?php
/**
 *
 */
class Melhorcotacao_Envio_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface
{


  protected $_code = 'melhorcotacao_envio';



  public function getAllowedMethods()
  {
      return array(
          'standard'    =>  'Standard delivery',
          'express'     =>  'Express delivery',
      );
  }

  public function collectRates(Mage_Shipping_Model_Rate_Request $request)
  {
  	/** @var Mage_Shipping_Model_Rate_Result $result */
  	$result = Mage::getModel('shipping/rate_result');

  	/** @var Inchoo_Shipping_Helper_Data $expressMaxProducts */
  	$expressMaxWeight = Mage::helper('envio')->getExpressMaxWeight();

  	//    $result->append($this->_getExpressRate());
  	  //  $result->append($this->_getStandardRate());

      $content = file_get_contents(__DIR__."/../controllers/Transportadoras.json");
      $file_decoded =  json_decode($content);
      $verify_shipping_companies = (array) $file_decoded;

        if ($file_decoded->show_in_front == 1 ) {
          $freights =  $this->getAllRates($request);
          foreach ($freights as $company) {
            foreach ($company->services as $iterator => $service) {
              if($service->status == 1){
                $soma_valor_dias = $service->delivery_time+$file_decoded->profit_days;
                $rate[$iterator] = Mage::getModel('shipping/rate_result_method');
                $rate[$iterator]->setCarrier($this->_code);
                $rate[$iterator]->setCarrierTitle($this->getConfigData('title'));
                $rate[$iterator]->setMethod($service->id."_".$company->name." ".$service->name);
                $rate[$iterator]->setMethodTitle($company->name." ".$service->name." - ( ".$soma_valor_dias." dias ) ");
                $rate[$iterator]->setPrice($service->price + (($file_decoded->profit_value/100)*$service->price) );
                //var_dump($service->price);
                $rate[$iterator]->setCost($service->price);
                foreach($verify_shipping_companies as $cc){
                    $logica = false;
                    if ($cc == $company->name) {
                      $logica = true; break;
                    }
                }
                if($logica){
                  $result->append($rate[$iterator]);
                }
              }
            }

          }

          if (empty($result->getAllRates())) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code)
                ->setCarrierTitle($this->getConfigData('title'))
                ->setErrorMessage(__("O volume de suas compras ultrapassa o limíte das transportadoras. Favor dividir o seu pedido."));

            $result->append($error);
          }
        }


  	return $result;
  }

  protected function getAllRates($requeest)
  {

    //Dados de Cadastro

    $keys = file_get_contents(__DIR__."/../controllers/Cadastraconfig.json");
    $decoded_keys = json_decode($keys);

    $content = file_get_contents(__DIR__."/../controllers/Transportadoras.json");
    $file_decoded = json_decode($content);

    $content2 = file_get_contents(__DIR__."/../controllers/Remetente.json");
    $rem_decoded = json_decode($content2);

    foreach ($requeest->getAllItems() as $pid) {
      $product = Mage::getModel("catalog/product")->load($pid->product_id);


      $weight        = $product->getData("weight");
      $height        = $product->getData("altura");
      $width         = $product->getData("largura");
      $length        = $product->getData("comprimento");


    //  $request->package->width  = $request->package->width + ($width  * $pid->getQty());
    // $request->package->height = $request->package->height+ ($height * $pid->getQty());
    //  $request->package->length = $request->package->length+ ($length * $pid->getQty());
    //  $request->package->weight = $request->package->weight+ ($weight * $pid->getQty());

        $volume  = $volume +  (int) ($width * $length * $height) * $pid->getQty();
        $request->package->weight = $request->package->weight+ $weight * $pid->getQty();

        $total += ($product->getData('price') * $pid->getQty());
    }

      $size   =  ceil(pow($volume,1/3));

      $request->package->width  = $request->package->width + $size;
      $request->package->height = $request->package->height+ $size;
      $request->package->length = $request->package->length+ $size;


      if($file_decoded->value_declaration == 1){

          $declared_value = (float) $total;
      } else{
          $declared_value = 0;
      }

      $own_hand = $file_decoded->own_hand;
      $receipt = $file_decoded->receipt;

      $request->from->postal_code = $rem_decoded->cep;
      $request->from->address =  $rem_decoded->address;
      $request->from->number = $rem_decoded->number;

      $request->to->postal_code = $requeest->getDestPostcode();
      $request->to->address =  $requeest->getDestStreet();
      $request->to->number =  $requeest->getDestStreet();

      $request->options->declared_value = $declared_value;
      $request->options->own_hand = $own_hand;
      $request->options->receipt = $receipt;

      $client = new Zend_Http_Client();
            $options = ['adapter'=>'Zend\Http\Client\Adapter\Curl',
                  'curloptions'  => [CURLOPT_FOLLOWLOCATION => true],
                  'timeout' => 30
      ];

      $client->setUri("https://api.melhorenvio.com.br/v1/shipping/calculate");
      $client->setMethod('POST');
      $client->setHeaders(['Content-Type'=>'application/json;charset=UTF-8']);
      $client->setParameterPost(array("name"=>"vivi","url"=>"http://melhorloja-parceira-do-melhorenvio.com.br"));
      $client->setAuth($decoded_keys->api_key,$decoded_keys->secret_token);
      //  echo "<pre>";
      //  var_dump($request); echo "</pre>";die;
      $request = (array) $request;
      $client->setParameterPost($request);


      return json_decode($client->request()->getBody());

}

  protected function _getStandardRate()
  {
    /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
    $rate = Mage::getModel('shipping/rate_result_method');

    $rate->setCarrier($this->_code);
    $rate->setCarrierTitle($this->getConfigData('title'));
    $rate->setMethod('larger');
    $rate->setMethodTitle('Standard delivery');
    $rate->setPrice(4.23);
    $rate->setCost(5);

    return $rate;
}
  protected function _getExpressRate()
  {
    /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
    $rate = Mage::getModel('shipping/rate_result_method');

    $rate->setCarrier($this->_code);
    $rate->setCarrierTitle($this->getConfigData('title'));
    $rate->setMethod('large');
    $rate->setMethodTitle('Express delivery');
    $rate->setPrice(1.23);
    $rate->setCost(5);

    return $rate;
}


}


 ?>
