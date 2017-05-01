<?php
/**
 * @package    Leytech_DeleteOrders
 * @author     Chris Nolan (chris@leytech.co.uk)
 * @copyright  Copyright (c) 2017 Leytech
 * @license    https://opensource.org/licenses/MIT  The MIT License  (MIT)
 */

class Leytech_DeleteOrders_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_IS_ENABLED = 'leytech_deleteorders/settings/enabled';

    protected $_enabled;

    public function isEnabled()
    {
        if (!isset($this->_enabled)) {
            return (bool)Mage::getStoreConfig(self::XML_PATH_IS_ENABLED);
        }
        return $this->_enabled;
    }

}