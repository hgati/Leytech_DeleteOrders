<?php
/**
 * @package    Leytech_DeleteOrders
 * @author     Chris Nolan (chris@leytech.co.uk)
 * @copyright  Copyright (c) 2017 Leytech
 * @license    https://opensource.org/licenses/MIT  The MIT License  (MIT)
 */

class Leytech_DeleteOrders_Adminhtml_DeleteordersController extends Mage_Adminhtml_Controller_Action
{
    public function massDeleteAction() {

        $helper = Mage::helper('leytech_deleteorders');

        // Don't delete if module is not enabled
        if(!$helper->isEnabled()) {
            $this->_getSession()->addError($this->__('Orders not deleted. Enable module under System -> Configuration -> Leytech Extensions -> Delete Orders.'));
            $this->_redirect('adminhtml/sales_order/', array());
            return;
        }

        // Don't delete if user doesn't have permission
        if (!Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/delete')) {
            $this->_getSession()->addError($this->__('You do not have the appropriate permissions to delete orders.'));
            $this->_redirect('adminhtml/sales_order/', array());
            return;
        }

        // Delete the requested orders
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $deletedOrders = 0;
        if ($orderIds) {
            foreach ($orderIds as $orderId) {
                $order = Mage::getModel('sales/order')->load($orderId);
                $transactionContainer = Mage::getModel('core/resource_transaction');
                if ($order->getId()) {

                    // Add associated invoices
                    if ($order->hasInvoices()){
                        $invoices = Mage::getResourceModel('sales/order_invoice_collection')->setOrderFilter($orderId)->load();
                        if ($invoices) {
                            foreach ($invoices as $invoice){
                                $invoice = Mage::getModel('sales/order_invoice')->load($invoice->getId());
                                $transactionContainer->addObject($invoice);
                            }
                        }
                    }

                    // Add associated shipments
                    if ($order->hasShipments()){
                        $shipments = Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($orderId)->load();
                        foreach ($shipments as $shipment){
                            $shipment = Mage::getModel('sales/order_shipment')->load($shipment->getId());
                            $transactionContainer->addObject($shipment);
                        }
                    }

                    // Delete
                    $transactionContainer->addObject($order)->delete();
                    $deletedOrders++;
                }
            }
        }

        // Show success message
        if ($deletedOrders) {
            $this->_getSession()->addSuccess($this->__('%s order(s) was/were successfully deleted.', $deletedOrders));
        }
        $this->_redirect('adminhtml/sales_order/', array());

    }

}