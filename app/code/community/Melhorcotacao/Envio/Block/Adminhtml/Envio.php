<?php
use Zend\Http\Client;
/**
 *
 */
class Melhorcotacao_Envio_Block_Adminhtml_Envio extends Mage_Adminhtml_Block_Template
{

  function __construct()
  {
    $this->_blockGroup = 'melhorcotacao_envio';
    $this->_controller = 'adminhtml_envio';
    $this->_headerText = __("Informações de Envio");
    parent::__construct();
  }

  public function getName(){



    $request_id = $this->getRequest()->getParam('order_id');
    $order      = Mage::getModel("sales/order")->load($request_id);
    $items = $order->getAllItems();


    $product_ids = array();

    foreach ($items as  $i=> $product){
      $pdt = Mage::getModel("catalog/product")->load(  $product->getProductId());
      echo $pdt->getData("name")."<br>";
    }

  }

    public function getImage(){

        $request_id = $this->getRequest()->getParam('order_id');
        $order      = Mage::getModel("sales/order")->load($request_id);
        $items = $order->getAllItems();

        $product = Mage::getModel("catalog/product")->load($items[0]->getProductId());
        try{
            echo $product->getImageUrl();
        }catch (exception $e){
            echo Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/image.jpg',array('_area'=>'frontend'));
        }

    }

  public function getCotacao(){



    $request_id = $this->getRequest()->getParam('order_id');
    $order      = Mage::getModel("sales/order")->load($request_id);
    $items = $order->getAllItems();


    $product_ids = array();




      $client = new Zend_Http_Client();
            $options = ['adapter'=>'Zend\Http\Client\Adapter\Curl',
                  'curloptions'  => [CURLOPT_FOLLOWLOCATION => true],
                  'timeout' => 30
      ];

      $keys = file_get_contents(__DIR__."/../../controllers/Cadastraconfig.json");
      $decoded_keys = json_decode($keys);

      $client->setUri("https://api.melhorenvio.com.br/v1/shipping");
      $client->setMethod('POST');
      $client->setHeaders(['Content-Type'=>'application/json;charset=UTF-8']);
      $client->setParameterPost(array("name"=>"vivi","url"=>"http://melhorloja-parceira-do-melhorenvio.com.br"));
      $client->setAuth($decoded_keys->api_key,$decoded_keys->secret_token);

      $request->email = $decoded_keys->email;
      $request->filter = array($request_id);




      $request = (array) $request;


      $client->setParameterPost($request);


     return $client->request()->getBody();




  }

  public function getCarrinho(){
    $carrinho = file_get_contents(__DIR__."/../../controllers/Carrinho.json");
    return $carrinho;
  }


}
