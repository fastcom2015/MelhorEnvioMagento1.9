<?php

/**
 *
 */
class Melhorcotacao_Envio_Block_Adminhtml_Index extends Mage_Adminhtml_Block_Widget_Form_Container
{

  function __construct()
  {
    $this->_blockGroup = 'melhorcotacao_envio_adminhtml';
    $this->_controller = 'index';
    $this->_headerText = __('Cadastro');
    $this->_mode = 'form';
    $this->setTemplate('envio/menu.phtml')->toHtml();
  }


  public function getSubsData()
  {
      $keys = file_get_contents(__DIR__."/../../controllers/Cadastraconfig.json");
      $decoded_keys = json_decode($keys);

      return $decoded_keys;

  }
  
}


 ?>
