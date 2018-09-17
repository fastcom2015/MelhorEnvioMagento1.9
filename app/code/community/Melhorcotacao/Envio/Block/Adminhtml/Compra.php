<?php
use Zend\Http\Client;
/**
 *
 */
class Melhorcotacao_Envio_Block_Adminhtml_Compra extends Mage_Adminhtml_Block_Template
{

    function __construct()
    {
        $this->_blockGroup = 'melhorcotacao_envio';
        $this->_controller = 'adminhtml_compra';



        parent::__construct();



    }

    public function getName(){
        $request_id = $this->getRequest()->getParam('order_id');
        $order      = Mage::getModel("sales/order")->load($request_id);
        $items = $order->getAllItems();


        $product_ids = array();

        foreach ($items as  $i=> $product){
            $pdt = Mage::getModel("catalog/product")->load(  $product->getProductId());
            echo $pdt->getData("name")." - ".(int)$product->getQtyOrdered()."<br>";

        }



    }

    public function getMethod(){
        $request_id = $this->getRequest()->getParam('order_id');
        $order      = Mage::getModel("sales/order")->load($request_id);
        $method = $order->getShippingMethod();
        return $method;
    }

    public function getCotacao(){

        $request_id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel("sales/order")->load($request_id);
        $items = $order->getAllItems();
        $products_ids = array();

        foreach ($items as  $i=> $product) {
            $product_ids[$i]->id = $product->getProductId();
            $product_ids[$i]->quantity = $product->getQtyOrdered();
        }

        //    var_dump($product_ids);

        foreach ($product_ids as $pid) {

            $product = Mage::getModel("catalog/product")->load($pid->id);

            $weight        = $product->getData("weight");
            $height        = $product->getData("altura");
            $width         = $product->getData("largura");
            $length        = $product->getData("comprimento");

            $volume  = $volume +  (int) ($width * $length * $height) * $pid->quantity;
            $request->package->weight = $request->package->weight+ $weight * $pid->quantity;

            $total += ($product->getData("price") * $pid->quantity);
        }

        $size   =  ceil(pow($volume,1/3));

        $request->package->width  = $request->package->width + $size;
        $request->package->height = $request->package->height+ $size;
        $request->package->length = $request->package->length+ $size;



        $keys = file_get_contents(__DIR__."/../../controllers/Cadastraconfig.json");
        $decoded_keys = json_decode($keys);


        $content = file_get_contents(__DIR__."/../../controllers/Transportadoras.json");
        $file_decoded = json_decode($content);



        if($file_decoded->value_declaration == 1){

            $declared_value = (float) $total;
        } else{
            $declared_value = 0;
        }


        $own_hand = $file_decoded->own_hand;
        $receipt = $file_decoded->receipt;


        $content2 = file_get_contents(__DIR__."/../../controllers/Remetente.json");
        $rem_decoded = json_decode($content2);


        $request->from->postal_code = $rem_decoded->cep;
        $request->from->address =  $rem_decoded->address;
        $request->from->number = $rem_decoded->number;



        //  $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        //  $adresses = Mage::getModel('sales/shipments')->load($)

        $address = $order->getShippingAddress();
        $request->to->postal_code = $address->getPostcode();
        $request->to->address =  $address->getStreetFull();
        $request->to->number = $address->getStreetFull();





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

        $request = (array) $request;
        $client->setParameterPost($request);
        return $client->request()->getBody();

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

    public function getIdCustomer(){
        return $this->getRequest()->getParam('order_id');
    }

}


?>
