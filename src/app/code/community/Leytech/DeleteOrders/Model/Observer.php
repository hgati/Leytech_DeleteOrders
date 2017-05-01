<?php
/**
 * @package    Leytech_DeleteOrders
 * @author     Chris Nolan (chris@leytech.co.uk)
 * @copyright  Copyright (c) 2017 Leytech
 * @license    https://opensource.org/licenses/MIT  The MIT License  (MIT)
 */

class Leytech_DeleteOrders_Model_Observer
{
    const SALES_ORDER_GRID_NAME = 'sales_order_grid';

    public function addMassOrderAction($observer)
    {
        $helper = Mage::helper('leytech_deleteorders');

        // Don't add option if module is not enabled
        if(!$helper->isEnabled()) {
            return $this;
        }

        // Don't add option if user doesn't have permission
        if (!Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/delete')) {
            return $this;
        }

        if ($observer->getEvent()->getBlock()->getId() == self::SALES_ORDER_GRID_NAME) {
            $block = $observer->getEvent()->getBlock()->getMassactionBlock();
            if ($block) {
                $block->addItem('leytech_deleteorders', array(
                    'label' => Mage::helper('sales')->__('Delete'),
                    'url' => $block->getUrl('*/deleteorders/massDelete'),
                    'confirm' => Mage::helper('sales')->__('Are you sure you wish to delete the selected orders?'),
                ));
            }
        }

        return $this;
    }
}