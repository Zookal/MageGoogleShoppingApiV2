<?php
/**
 *
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * GoogleShopping Admin Items Controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Adminhtml_GShoppingV2_ItemsController
    extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        /**
         * trick autoloader because the class Google_Service_ShoppingContent is included in the file
         * Google/Service/ShoppingContent.php which contains all other classes Google_Service_ShoppingContent_*
         */
        new Google_Service_ShoppingContent(Mage::getSingleton('gshoppingv2/googleShopping')->getClient(null));
    }

    /**
     * Initialize general settings for action
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/gshoppingv2/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog'), Mage::helper('adminhtml')->__('Catalog'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Google Content'), Mage::helper('adminhtml')->__('Google Content'));
        return $this;
    }

    /**
     * Manage Items page with two item grids: Magento products and Google Content items
     */
    public function indexAction()
    {

        $this->_title($this->__('Catalog'))
            ->_title($this->__('Google Content'))
            ->_title($this->__('Manage Items'));

        if (0 === (int)$this->getRequest()->getParam('store')) {
            $this->_redirect('*/*/', ['store' => Mage::app()->getAnyStoreView()->getId(), '_current' => true]);
            return;
        }

        $storeId = $this->_getStoreId();

        if ($storeId) {
            $service = Mage::getModel('gshoppingv2/googleShopping');
            $service->getClient($storeId);
        }

        $contentBlock = $this->getLayout()->createBlock('gshoppingv2/adminhtml_items')->setStore($this->_getStore());

        if (!$this->_getConfig()->isValidDefaultCurrencyCode($this->_getStore()->getId())) {
            $_countryInfo = $this->_getConfig()->getTargetCountryInfo($this->_getStore()->getId());
            $this->_getSession()->addNotice(
                Mage::helper('gshoppingv2')->__("The store's currency should be set to %s for %s in system configuration. Otherwise item prices won't be correct in Google Content.", $_countryInfo['currency_name'], $_countryInfo['name'])
            );
        }

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('gshoppingv2')->__('Items'), Mage::helper('gshoppingv2')->__('Items'))
            ->_addContent($contentBlock)
            ->renderLayout();
    }

    /**
     * Grid with Google Content items
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('gshoppingv2/adminhtml_items_item')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
        );
    }

    /**
     * Retrieve synchronization process mutex
     *
     * @return Zookal_GShoppingV2_Model_Flag
     */
    protected function _getFlag()
    {
        return Mage::getSingleton('gshoppingv2/flag')->loadSelf();
    }

    /**
     * Add (export) several products to Google Content
     */
    public function massAddAction()
    {
        $storeId = $this->_getStore()->getId();

        $flag = $this->_getFlag();
        if ($flag->isLocked()) {
            $this->_getSession()->addError($this->__('Flag locked!'));
            $this->_redirect('*/*/index', ['store' => $storeId]);
            return;
        }

        $productIds = $this->getRequest()->getParam('product', null);
        $notifier   = Mage::getModel('adminnotification/inbox');

        try {
            $flag->lock();
            Mage::getModel('gshoppingv2/massOperations')
                ->setFlag($flag)
                ->addProducts($productIds, $storeId);
        } catch (Exception $e) {
            $flag->unlock();
            $notifier->addMajor(
                Mage::helper('gshoppingv2')->__('An error has occured while adding products to google shopping account.'),
                $e->getMessage()
            );
            Mage::logException($e);
            $this->_redirect('*/*/index', ['store' => $storeId]);
            return $this;
        }

        $flag->unlock();

        $this->_redirect('*/*/index', ['store' => $storeId]);
        return $this;
    }

    /**
     * Delete products from Google Content
     */
    public function massDeleteAction()
    {
        $storeId = $this->_getStore()->getId();

        $flag = $this->_getFlag();
        if ($flag->isLocked()) {
            $this->_getSession()->addError($this->__('Flag locked!'));
            $this->_redirect('*/*/index', ['store' => $storeId]);
            return;
        }

        $itemIds = $this->getRequest()->getParam('item');

        try {
            $flag->lock();
            Mage::getModel('gshoppingv2/massOperations')
                ->setFlag($flag)
                ->deleteItems($itemIds);
        } catch (Exception $e) {
            $flag->unlock();
            Mage::getModel('adminnotification/inbox')->addMajor(
                Mage::helper('gshoppingv2')->__('An error has occured while deleting products from google shopping account.'),
                Mage::helper('gshoppingv2')->__('One or more products were not deleted from google shopping account. Refer to the log file for details.')
            );
            Mage::logException($e);
            $this->_redirect('*/*/index', ['store' => $storeId]);
            return $this;
        }
        $this->_redirect('*/*/index', ['store' => $storeId]);
        $flag->unlock();
        return $this;
    }

    /**
     * Update items statistics and remove the items which are not available in Google Content
     */
    public function refreshAction()
    {
        $storeId = $this->_getStore()->getId();

        $flag = $this->_getFlag();

        if ($flag->isLocked()) {
            $this->_getSession()->addError($this->__('Flag locked!'));
            $this->_redirect('*/*/index', ['store' => $storeId]);
            return;
        }

        $itemIds = $this->getRequest()->getParam('item');

        try {
            $flag->lock();
            Mage::getModel('gshoppingv2/massOperations')
                ->setFlag($flag)
                ->synchronizeItems($itemIds);
        } catch (Exception $e) {
            $flag->unlock();
            Mage::getModel('adminnotification/inbox')->addMajor(
                Mage::helper('gshoppingv2')->__('An error has occured while deleting products from google shopping account.'),
                Mage::helper('gshoppingv2')->__('One or more products were not deleted from google shopping account. Refer to the log file for details.')
            );
            Mage::logException($e);
            return $this;
        }
        $flag->unlock();

        $this->_redirect('*/*/index', ['store' => $storeId]);
        return $this;
    }

    /**
     * Retrieve background process status
     *
     * @return Zend_Controller_Response_Abstract
     */
    public function statusAction()
    {
        if ($this->getRequest()->isAjax()) {
            $this->getResponse()->setHeader('Content-Type', 'application/json');
            $params = [
                'is_running' => $this->_getFlag()->isLocked()
            ];
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($params));
        }
    }

    /**
     * Get store object, basing on request
     *
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Exception
     */
    public function _getStore()
    {
        $store = Mage::app()->getStore((int)$this->getRequest()->getParam('store', 0));
        if ((!$store) || 0 == $store->getId()) {
            Mage::throwException($this->__('Unable to select a Store View.'));
        }
        return $store;
    }

    public function _getStoreId()
    {
        $store = Mage::app()->getStore((int)$this->getRequest()->getParam('store', 0));
        if ($store && $store->getId()) {
            return $store->getId();
        }
        return null;
    }

    /**
     * Get Google Shopping config model
     *
     * @return Zookal_GShoppingV2_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('gshoppingv2/config');
    }

    /**
     * Check access to this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/gshoppingv2/items');
    }
}
