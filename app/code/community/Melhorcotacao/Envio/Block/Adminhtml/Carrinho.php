<?php

/**
 *
 */
class Melhorcotacao_Envio_Block_Adminhtml_Carrinho extends Mage_Adminhtml_Block_Widget_Grid_Container
{

  function __construct()
  {
    $this->_blockGroup = 'melhorcotacao_envio_adminhtml';
    $this->_controller = 'carrinho';
    $this->_headerText = __('Carrinho');
    $this->_mode = 'grid';
  //  $this->setTemplate('envio/config.phtml')->toHtml();

    parent::__construct();

    $product = Mage::getModel("catalog/product")->load(1);

    echo $product->getData("name");



  }


  public function getProduct(){

    $product = Mage::getModel("catalog/product")->load(1);

    echo $product->getData("name");

  }

  public function getCotacao(){


  }


}
