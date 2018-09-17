<?php
use Zend\Http\Client;

class Melhorcotacao_Envio_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action{

    /**
     * @access public
     *
     * @return void
     */

//Actions relacionadas com configuração de usuário
    public function indexAction(){
      $compras = $this->getUrl("/adminhtml_index/relatorio");
      $preferencia = $this->getUrl("/adminhtml_index/preferencias");
      $cadastro = $this->getUrl("/adminhtml_index/index");
      $formAction = $this->getCarrinho();

      $teste = $this->getUrl("/adminhtml_index/teste");

      $brandBlock = $this->getLayout()
          ->createBlock('melhorcotacao_envio_adminhtml/Index');

        $this->loadLayout()->_addContent($brandBlock);
        $brandBlock->setTemplate('envio/menu.phtml')->toHtml();



        $this->_setActiveMenu('envio');
        $this->_title($this->__('Melhor Envio'));

        $this->_addLeft($this->getLayout()
                ->createBlock('core/text')->setText("<h2><a href='$cadastro'> Cadastro </a></h2>"));

        $this->_addLeft($this->getLayout()
                ->createBlock('core/text')->setText("<h2><a href='$preferencia'> Preferencias </a></h2>"));


        $this->_addLeft($this->getLayout()
                ->createBlock('core/text')->setText("<h2><a href='$compras'> Pedidos </a></h2>"));

        $this->_addLeft($this->getLayout()
                ->createBlock('core/text')->setText("<h2><a href='$formAction'> Carrinho </a></h2>"));

        $this->renderLayout();


    }

    public function salvaAction(){

      $client = new Zend_Http_Client();


      $client->setUri("https://api.melhorenvio.com.br/v1/authorize");
      $client->setMethod('POST');
      $client->setHeaders(['Content-Type'=>'application/json;charset=UTF-8']);
      $client->setParameterPost(array("name"=>Mage::app()->getStore()->getName(),"url"=>Mage::getBaseUrl()));

      $resposta =  json_decode($client->request()->getBody());
        //  var_dump($resposta);
          $email = $this->getRequest()->getPost('email');
          $firstname =  $this->getRequest()->getPost('firstname');
          $lastname = $this->getRequest()->getPost('lastname');
          $phone1 = $this->getRequest()->getPost('phone');
          $postal_code = $this->getRequest()->getPost('postal_code');
          $address = $this->getRequest()->getPost('address');
          $number = $this->getRequest()->getPost('number');
          $district = $this->getRequest()->getPost('district');
          $city = $this->getRequest()->getPost('city');
          $uf = $this->getRequest()->getPost('uf');
          $complement = $this->getRequest()->getPost('complement');
          $cpf = $this->getRequest()->getPost('cpf');
          $cnpj = $this->getRequest()->getPost('CNPJ');
          $ie = $this->getRequest()->getPost('IE');


        $client->setUri("https://api.melhorenvio.com.br/v1/clients");
          $parameters =  array("email"=>$email,"firstname"=>$firstname,"lastname"=>$lastname,"phone1"=>$phone1,"postal_code"=>$postal_code,"address"=>$address,"number"=>$number,"district"=>$district,"city"=>$city,"uf"=>$uf,"complement"=>$complement,"cpf"=>$cpf,"cnpj"=>$cnpj,"ie"=>$ie);


          $client->setAuth($resposta->api_key,$resposta->secret_token);
          $client->setParameterPost($parameters);


          $resposta->email = $email;
          $resposta->firstname = $firstname;
          $resposta->cpf = $cpf;
          $resposta->telefone = $phone1;
          $resposta->cnpj = $cnpj;
          $resposta->ie = $ie;
        
          //TODO Adicionar a mensagem e o redirecionamento

      if($client->request()->isSuccessful()){

        Mage::getSingleton('adminhtml/session')->addWarning('Cadastrado com sucesso');

        $keytexto = json_encode($resposta);

        $file = fopen(__DIR__."/../Cadastraconfig.json","w+");
        fwrite($file , $keytexto);
        fclose($file);


            session_write_close();
        $this->_redirect("/adminhtml_index/preferencias");
      }else{
        Mage::getSingleton('core/session')->addError('Erro no Cadastro, tente novamente mais tarde!');
            session_write_close();
        $this->_redirect("/adminhtml_index/index");
      }


    }

    public function preferenciasAction(){

      $this->verifySub();

      $compras = $this->getUrl("/adminhtml_index/relatorio");
      $preferencia = $this->getUrl("/adminhtml_index/preferencias");
      $cadastro = $this->getUrl("/adminhtml_index/index");
      $formAction = $this->getCarrinho();

      $this->loadLayout();


      $this->_setActiveMenu('envio');
      $brandBlock = $this->getLayout()
          ->createBlock('melhorcotacao_envio_adminhtml/Preferencias');

          $brandBlock->setTemplate('envio/config.phtml')->toHtml();

          $this->loadLayout()->_addContent($brandBlock);

                  $this->_setActiveMenu('envio');
                  $this->_title($this->__('Melhor Envio'));

                  $this->_addLeft($this->getLayout()
                          ->createBlock('core/text')->setText("<h2><a href='$cadastro'> Cadastro </a></h2>"));

                  $this->_addLeft($this->getLayout()
                          ->createBlock('core/text')->setText("<h2><a href='$preferencia'> Preferencias </a></h2>"));


                  $this->_addLeft($this->getLayout()
                          ->createBlock('core/text')->setText("<h2><a href='$compras'> Pedidos </a></h2>"));

                  $this->_addLeft($this->getLayout()
                          ->createBlock('core/text')->setText("<h2><a href='$formAction'> Carrinho </a></h2>"));

                $this->renderLayout();
    }

    public function salvaremetenteAction(){

      $cep = $this->getRequest()->getPost('cep');
      $address = $this->getRequest()->getPost('address');
      $number = $this->getrequest()->getPost('number');
      $district = $this->getRequest()->getPost('district');
      $city = $this->getRequest()->getPost('city');
      $uf = $this->getRequest()->getPost('uf');

      $resposta->cep = $cep;
      $resposta->address = $address;
      $resposta->number =$number;
      $resposta->district = $district;
      $resposta->city = $city;
      $resposta->uf = $uf;

      $keytexto = json_encode($resposta);
      try{
      $file = fopen(__DIR__."/../Remetente.json","w+");
      fwrite($file , $keytexto);
      fclose($file);
      Mage::getSingleton('adminhtml/session')->addSuccess('Success message');
      } catch(Exception $e){
        Mage::getSingleton('adminhtml/session')->addError('Error message');
      }



      $this->_redirect("/adminhtml_index/preferencias");

    }

    public function salvaconfAction(){

          //      $show_in_front = $this->getRequest()->getPost('show_in_front');
          //      $show_discount = $this->getRequest()->getPost('show_discount');

          $save = json_encode($this->getRequest()->getPost());

          try{
          $file = fopen(__DIR__."/../Transportadoras.json","w+");
          fwrite($file , $save);
          fclose($file);



          Mage::getSingleton('adminhtml/session')->addSuccess('Success message');
          } catch(Exception $e){
            Mage::getSingleton('adminhtml/session')->addError('Error message');
          }

          $this->_redirect("/adminhtml_index/preferencias");


    }


//Actions voltadas para exibição do carrinho de compras e relatórios de envios

    public function carrinhoAction(){

      $compras = $this->getUrl("/adminhtml_index/relatorio");
      $preferencia = $this->getUrl("/adminhtml_index/preferencias");
      $cadastro = $this->getUrl("/adminhtml_index/index");
      $formAction = $this->getCarrinho();


      $this->loadLayout();

      $this->_setActiveMenu('envio');
      $this->_title($this->__('Melhor Envio'));

      $brandBlock = $this->getLayout()
          ->createBlock('melhorcotacao_envio_adminhtml/Carrinho');

        $this->loadLayout()->_addContent($brandBlock);
        //$brandBlock->setTemplate('envio/menu.phtml')->toHtml();




                $this->_setActiveMenu('envio');
                $this->_title($this->__('Melhor Envio'));

                $this->_addLeft($this->getLayout()
                        ->createBlock('core/text')->setText("<h2><a href='$cadastro'> Cadastro </a></h2>"));

                $this->_addLeft($this->getLayout()
                        ->createBlock('core/text')->setText("<h2><a href='$preferencia'> Preferencias </a></h2>"));


                $this->_addLeft($this->getLayout()
                        ->createBlock('core/text')->setText("<h2><a href='$compras'> Pedidos </a></h2>"));

                $this->_addLeft($this->getLayout()
                        ->createBlock('core/text')->setText("<h2><a href='$formAction'> Carrinho </a></h2>"));

        $this->renderLayout();

    }

    public function verificaAction(){

      $compras = $this->getUrl("/adminhtml_index/relatorio");
      $preferencia = $this->getUrl("/adminhtml_index/preferencias");
      $cadastro = $this->getUrl("/adminhtml_index/index");
      $formAction = $this->getCarrinho();

            $teste = $this->getUrl("/adminhtml_index/teste");

            $brandBlock = $this->getLayout()
                ->createBlock('melhorcotacao_envio_adminhtml/Envio');

              $this->loadLayout()->_addContent($brandBlock);
              $brandBlock->setTemplate('envio/envio.phtml')->toHtml();

              $this->_setActiveMenu('envio');
              $this->_title($this->__('Melhor Envio'));


                              $this->_addLeft($this->getLayout()
                                      ->createBlock('core/text')->setText("<h2><a href='$cadastro'> Cadastro </a></h2>"));

                              $this->_addLeft($this->getLayout()
                                      ->createBlock('core/text')->setText("<h2><a href='$preferencia'> Preferencias </a></h2>"));


                              $this->_addLeft($this->getLayout()
                                      ->createBlock('core/text')->setText("<h2><a href='$compras'> Pedidos </a></h2>"));

                              $this->_addLeft($this->getLayout()
                                      ->createBlock('core/text')->setText("<h2><a href='$formAction'> Carrinho </a></h2>"));

              $this->renderLayout();
    }

    public function relatorioAction(){

      $this->verifySub();

      $compras = $this->getUrl("/adminhtml_index/relatorio");
      $preferencia = $this->getUrl("/adminhtml_index/preferencias");
      $cadastro = $this->getUrl("/adminhtml_index/index");
      $formAction = $this->getCarrinho();


      $this->loadLayout();

      $this->_setActiveMenu('envio');
      $this->_title($this->__('Melhor Envio'));

      $brandBlock = $this->getLayout()
          ->createBlock('melhorcotacao_envio_adminhtml/Relatorio');

        $this->loadLayout()->_addContent($brandBlock);
        //$brandBlock->setTemplate('envio/menu.phtml')->toHtml();




                        $this->_addLeft($this->getLayout()
                                ->createBlock('core/text')->setText("<h2><a href='$cadastro'> Cadastro </a></h2>"));

                        $this->_addLeft($this->getLayout()
                                ->createBlock('core/text')->setText("<h2><a href='$preferencia'> Preferencias </a></h2>"));


                        $this->_addLeft($this->getLayout()
                                ->createBlock('core/text')->setText("<h2><a href='$compras'> Pedidos </a></h2>"));

                        $this->_addLeft($this->getLayout()
                                ->createBlock('core/text')->setText("<h2><a href='$formAction'> Carrinho </a></h2>"));

        $this->renderLayout();
    }

// Actions
    public function produtoAction(){
      $this->verifySub();

      $request_id = $this->getRequest()->getParam('order_id');
      $order = Mage::getModel("sales/order")->load($request_id);


        $client = new Zend_Http_Client();


        $keys = file_get_contents(__DIR__."/../../controllers/Cadastraconfig.json");
        $decoded_keys = json_decode($keys);

        $client->setUri("https://api.melhorenvio.com.br/v1/shipping");
        $client->setMethod('POST');
        $client->setHeaders(['Content-Type'=>'application/json;charset=UTF-8']);
        $client->setParameterPost(array("name"=>Mage::app()->getStore()->getName(),"url"=>Mage::getBaseUrl()));
        $client->setAuth($decoded_keys->api_key,$decoded_keys->secret_token);

        $request->email = $decoded_keys->email;
        $request->filter = array($request_id);

        $request = (array) $request;


        $client->setParameterPost($request);
        $cond = json_decode($client->request()->getBody());

        if(!empty($cond)){
      $this->_redirect("/adminhtml_index/verifica",array('order_id'=> $request_id));

      }else{

        $compras = $this->getUrl("/adminhtml_index/relatorio");
        $preferencia = $this->getUrl("/adminhtml_index/preferencias");
        $cadastro = $this->getUrl("/adminhtml_index/index");
        $formAction = $this->getCarrinho();

      $teste = $this->getUrl("/adminhtml_index/teste");

      $brandBlock = $this->getLayout()
          ->createBlock('melhorcotacao_envio_adminhtml/Compra');

        $this->loadLayout()->_addContent($brandBlock);
        $brandBlock->setTemplate('envio/compra.phtml')->toHtml();

        $this->_setActiveMenu('envio');
        $this->_title($this->__('Melhor Envio'));


                $this->_setActiveMenu('envio');
                $this->_title($this->__('Melhor Envio'));

                $this->_addLeft($this->getLayout()
                        ->createBlock('core/text')->setText("<h2><a href='$cadastro'> Cadastro </a></h2>"));

                $this->_addLeft($this->getLayout()
                        ->createBlock('core/text')->setText("<h2><a href='$preferencia'> Preferencias </a></h2>"));


                $this->_addLeft($this->getLayout()
                        ->createBlock('core/text')->setText("<h2><a href='$compras'> Pedidos </a></h2>"));

                $this->_addLeft($this->getLayout()
                        ->createBlock('core/text')->setText("<h2><a href='$formAction'> Carrinho </a></h2>"));
        $this->renderLayout();

    }}

//Compra na API
    public function buyshipAction(){

      $request_id = $this->getRequest()->getParam('compra_id');
      $order = Mage::getModel("sales/order")->load($request_id);


        $client = new Zend_Http_Client();


        $keys = file_get_contents(__DIR__."/../../controllers/Cadastraconfig.json");
        $decoded_keys = json_decode($keys);

        $client->setUri("https://api.melhorenvio.com.br/v1/shipping");
        $client->setMethod('POST');
        $client->setHeaders(['Content-Type'=>'application/json;charset=UTF-8']);
        $client->setParameterPost(array("name"=>Mage::app()->getStore()->getName(),"url"=>Mage::getBaseUrl()));
        $client->setAuth($decoded_keys->api_key,$decoded_keys->secret_token);

        $request->email = $decoded_keys->email;
        $request->filter = array($request_id);

        $requeest = (array) $request;


        $client->setParameterPost($requeest);

        $cond = json_decode($client->request()->getBody());
      if(!empty($cond)){
        $this->_redirect("/adminhtml_index/relatorio");
      }else{

      $items = $order->getAllItems();
      $products_ids = array();

      foreach ($items as  $i=> $product) {
        $product_ids[$i]->id = $product->getProductId();
        $product_ids[$i]->quantity = $product->getQtyOrdered();
      }

      foreach ($product_ids as $pid) {

        $product = Mage::getModel("catalog/product")->load($pid->id);

        $weight        = $product->getData("weight");
        $height        = $product->getData("altura");
        $width         = $product->getData("largura");
        $length        = $product->getData("comprimento");

          $volume  = $volume +  (int) ($width * $length * $height) * $pid->quantity;
          $request->package->weight = $request->package->weight+ $weight * $pid->quantity;

          $total += ($product->getData('price') * $pid->quantity);
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

      //Dados do dono do Ecommerce

      $request->from->name        = $decoded_keys->firstname;
      $request->from->phone       = $decoded_keys->phone;
      $request->from->postal_code = $rem_decoded->cep;
      $request->from->address     = $rem_decoded->address;
      $request->from->number      = $rem_decoded->number;
      $request->from->district    = $rem_decoded->address;
      $request->from->city        = $rem_decoded->city;
      $request->from->uf          = $rem_decoded->uf;
      $request->from->complement  = $rem_decoded->complement;

      $request->from->cpf         = $decoded_keys->cpf;
      $request->from->cnpj        = $decoded_keys->cnpj;
      $request->from->ie          = $decoded_keys->ie;

      //Dados do serviço

      $request->service  = $this->getRequest()->getPost("service");
      //Dados do Cliente do Dono do Ecommerce
      $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());

      $address = $order->getShippingAddress();
      $request->to->name        = $address->getName();

      $request->to->phone       = $address->getTelephone();
      $request->to->postal_code = $address->getPostcode();
      $request->to->address     = $address->getStreet1();
      $request->to->number      = $address->getStreet2();
      $request->to->district    = $address->getCompany();
      $request->to->city        = $address->getCity();
      $request->to->uf          = $address->getRegion();

      $request->optionals->declared_value = $declared_value;
      $request->optionals->own_hand = $own_hand;
      $request->optionals->receipt = $receipt;

        $request->ref = $request_id;

      $client = new Zend_Http_Client();


      $client->setUri("https://api.melhorenvio.com.br/v1/shipping/cart");
      $client->setMethod('POST');
      $client->setHeaders(['Content-Type'=>'application/json;charset=UTF-8']);



      $content = file_get_contents(__DIR__."/../Cadastraconfig.json");
      $file_decoded = json_decode($content);

      $api_key = $file_decoded->api_key;
      $secret_token = $file_decoded->secret_token;
      $email = $file_decoded->email;

      $client->setAuth($api_key,$secret_token);

      $cart =  (array) $request;

      $array = array($cart);
      $rqst = array('email' => $email,'cart' => $array);

      $client->setParameterPost($rqst);


      $salvar = json_decode($client->request()->getBody());
      $file = fopen(__DIR__."/../Carrinho.json","w+");
      fwrite($file ,$salvar->url);
      fclose($file);


      }
      //  $this->_redirect("/adminhtml_index/relatorio");

      $cadastro = $this->getUrl("/adminhtml_index/relatorio");
      $teste = $salvar->url;

      $brandBlock = $this->getLayout()
          ->createBlock('melhorcotacao_envio_adminhtml/Index');

          $this->loadLayout()->_addContent($brandBlock);
          $brandBlock->setTemplate('envio/new.phtml')->toHtml();

        $this->_addContent($this->getLayout()
                ->createBlock('core/text')->setText("<h2><a href='$cadastro'> Voltar para o seu Painel </a>  "));

        $this->_addContent($this->getLayout()
                ->createBlock('core/text')->setText(" || <a href='$teste' target='_blank'> Ir para a Melhor Envio </a></h2>"));

        $this->renderLayout();


  }

  public function getCarrinho(){
    $carrinho = file_get_contents(__DIR__."/../../controllers/Carrinho.json");
    return $carrinho;
  }

  public function verifySub(){
    $content = json_decode(file_get_contents(__DIR__."/../Cadastraconfig.json"));

    if($content->api_key == null){
        $this->_redirect("/adminhtml_index/index");
    }
  }

}
