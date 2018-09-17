<?php

/**
 *
 */
class Melhorcotacao_Envio_Block_Adminhtml_Preferencias extends Mage_Adminhtml_Block_Widget_Form_Container
{

  function __construct()
  {
    $this->_blockGroup = 'melhorcotacao_envio_adminhtml';
    $this->_controller = 'preferencias';
    $this->_headerText = __('Preferencias');
    $this->_mode = 'form';
    $this->setTemplate('envio/config.phtml')->toHtml();

    //parent::__construct();

  }

  public function getTransportadoras(){

    $client = new Zend_Http_Client();
          $options = ['adapter'=>'Zend\Http\Client\Adapter\Curl',
                      'curloptions'  => [CURLOPT_FOLLOWLOCATION => true],
                      'timeout' => 30
          ];

          $client->setUri("https://api.melhorenvio.com.br/v1/shipping/services");
          $client->setMethod('POST');
          $client->setHeaders(['Content-Type'=>'application/json;charset=UTF-8']);
          $client->setParameterPost(array("name"=>"vivi","url"=>"'https://api.melhorenvio.com.br/v1/shipping/services'"));

          $client->request();

          return json_decode($client->request()->getBody());

  }

  public function getEmail()
  {

    $content = file_get_contents(__DIR__."/../../controllers/Cadastraconfig.json");
    $file_decoded = json_decode($content);

    return $file_decoded->email;

  }

  public function getEndereco()
  {

    $content = file_get_contents(__DIR__."/../../controllers/Remetente.json");
    $file_decoded = json_decode($content);

    return $file_decoded;

  }

  public function getProfit()
  {
    $content = file_get_contents(__DIR__."/../../controllers/Transportadoras.json");
    $file_decoded = json_decode($content);
    return $file_decoded->profit_value;

  }

    public function getProfit_Days()
    {
        $content = file_get_contents(__DIR__."/../../controllers/Transportadoras.json");
        $file_decoded = json_decode($content);
        return $file_decoded->profit_days;

    }

    public function getSaved()
    {
        $content = file_get_contents(__DIR__."/../../controllers/Transportadoras.json");
        $file_decoded = json_decode($content);
        return (array) $file_decoded;

    }

}


 ?>
