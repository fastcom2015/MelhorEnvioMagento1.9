<?php

/**
 *
 */
class Melhorcotacao_Envio_Block_Adminhtml_Form_Index extends Mage_Adminhtml_Block_Widget_Form
{

  echo "oi";
  protected function _prepareForm(){
    $form = new Varien_Data_Form(array(
      'id' => 'edit_form',
      'action' => $this->getUrl(
          'melhorcotacao_envio_adminhtml/index/salva',
            array(
                '_current' => true,
                'continue' => 0,
                )
            ),
            'method' => 'post',

    ));

    $form->setUseContainer(true);
    $this->setForm($form);

    $fieldset = $form->addFieldset(
            'general',
            array(
                'legend' => $this->__('Preferencias')
            )
        );
  }



}


 ?>
