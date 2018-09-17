<?php

class Melhorcotacao_Envio_Block_Adminhtml_Carrinho_Grid extends Mage_Adminhtml_Block_Widget_Grid{
  public function __construct()
  {
        parent::__construct();

        $this->setId('relatorio_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);

  }

  protected function _getCollectionClass()
  {
      return 'sales/order_grid_collection';
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getResourceModel($this->_getCollectionClass());
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  public function getRowUrl($row)
  {
      if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
          return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
      }
      return false;
  }

  protected function _prepareColumns()
  {
        $helper = Mage::helper('envio');

        $this->addColumn('id', array(
            'header' => $helper->__('Número do Pedido'),
            'index'  => 'increment_id'
        ));

        $this->addColumn('name', array(
            'header' => $helper->__('Nome do cliente'),
            'index'  => 'shipping_name'
        ));

        $this->addColumn('status', array(
            'header' => $helper->__('Status'),
            'index'  => 'status'
        ));

        $this->addColumn('purchased_on', array(
            'header' => $helper->__('Purchased On'),
            'type'   => 'datetime',
            'index'  => 'created_at'
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id',
                            'data-column' => 'action',
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }



        return parent::_prepareColumns();
    }



}
