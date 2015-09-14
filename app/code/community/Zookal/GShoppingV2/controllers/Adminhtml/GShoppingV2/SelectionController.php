<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * GoogleShopping Products selection grid controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Adminhtml_GShoppingV2_SelectionController extends
    Mage_Adminhtml_Controller_Action
{
    /**
     * Search result grid with available products for Google Content
     */
    public function searchAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('gshoppingv2/adminhtml_items_product')
                ->setIndex($this->getRequest()->getParam('index'))
                ->setFirstShow(true)
                ->toHtml()
        );
    }

    /**
     * Grid with available products for Google Content
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('gshoppingv2/adminhtml_items_product')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
        );
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/gshoppingv2/items');
    }
}
