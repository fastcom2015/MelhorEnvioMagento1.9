<?php

class Melhorcotacao_Envio_Helper_Data extends Mage_Core_Helper_Abstract {

     const XML_EXPRESS_MAX_WEIGHT = 'carriers/melhorcotacao_envio/express_max_weight';

      public function getExpressMaxWeight()
      {
          return Mage::getStoreConfig(self::XML_EXPRESS_MAX_WEIGHT);
      }

}
