<?php

/**
 *
 */
class Melhorcotacao_Envio_Block_Adminhtml_Relatorio extends Mage_Adminhtml_Block_Widget_Grid_Container
{

  function __construct()
  {
    $this->_blockGroup = 'melhorcotacao_envio_adminhtml';
    $this->_controller = 'relatorio';
    $this->_headerText = __('Relatório');
    $this->_mode = 'grid';

  //  $this->setTemplate('envio/config.phtml')->toHtml();
    parent::__construct();

    $this->_removeButton('add');
  }







}
