<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Shopping Observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Observer
{
    /**
     * Update product item in Google Content
     *
     * @dispatch catalog_product_save_after
     *
     * @param Varien_Object $observer
     *
     * @return Zookal_GShoppingV2_Model_Observer
     */
    public function saveProductItem($observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getProduct();
        /** @var Zookal_GShoppingV2_Model_Resource_Item_Collection $items */
        $items = $this->_getItemsCollection($product);

        try {
            Mage::getModel('gshoppingv2/massOperations')->synchronizeItems($items);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')
                ->addError('Cannot update Google Content Item. Google requires CAPTCHA.');
        }

        return $this;
    }

    /**
     * Delete product item from Google Content
     * @dispatch catalog_product_delete_before
     *
     * @param Varien_Object $observer
     *
     * @return Zookal_GShoppingV2_Model_Observer
     */
    public function deleteProductItem($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $items   = $this->_getItemsCollection($product);

        try {
            Mage::getModel('gshoppingv2/massOperations')->deleteItems($items);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')
                ->addError('Cannot delete Google Content Item. Google requires CAPTCHA.');
        }

        return $this;
    }

    /**
     * Get items which are available for update/delete when product is saved
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return Zookal_GShoppingV2_Model_Resource_Item_Collection
     */
    protected function XXX_getItemsCollection($product)
    {
        $items = Mage::getResourceModel('gshoppingv2/item_collection')
            ->addProductFilterId($product->getId());
        if ($product->getStoreId()) {
            $items->addStoreFilter($product->getStoreId());
        }

        foreach ($items as $item) {
            if (!Mage::getStoreConfigFlag('google/gshoppingv2/observed', $item->getStoreId())) {
                $items->removeItemByKey($item->getId());
            }
        }

        return $items;
    }

    /**
     * Get items which are available for update/delete when product is saved
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return Zookal_GShoppingV2_Model_Resource_Item_Collection
     */
    protected function _getItemsCollection(Mage_Catalog_Model_Product $product)
    {
        $items = Mage::getResourceModel('gshoppingv2/item_collection')
            ->addProductFilterId($product->getId());
        if ($product->getStoreId()) {
            $items->addStoreFilter($product->getStoreId());
        }

        $clientAuthenticated = true;
        foreach ($items as $item) {
            if (!Mage::getStoreConfigFlag('catalog/gshoppingv2/observed', $item->getStoreId())) {
                $items->removeItemByKey($item->getId());
            } else {
                if (Mage::getSingleton('gshoppingv2/googleShopping')->isAuthenticated($item->getStoreId())) {
                    $items->removeItemByKey($item->getId());
                } else {
                    $clientAuthenticated = false;
                }
            }
        }
        if (false === $clientAuthenticated) {
            Mage::getSingleton('adminhtml/session')->addWarning(
                Mage::helper('gshoppingv2')->__('Product was not updated on GoogleShopping for at least one store. Please authenticate and save the product again or update manually.')
            );
        }

        return $items;
    }

    /**
     * Check if synchronize process is finished and generate notification message
     *
     * @param  Varien_Event_Observer $observer
     *
     * @return Zookal_GShoppingV2_Model_Observer
     */
    public function checkSynchronizationOperations(Varien_Event_Observer $observer)
    {
        $flag = Mage::getSingleton('gshoppingv2/flag')->loadSelf();
        if ($flag->isExpired()) {
            Mage::getModel('adminnotification/inbox')->addMajor(
                Mage::helper('gshoppingv2')->__('Google Shopping operation has expired.'),
                Mage::helper('gshoppingv2')->__('One or more google shopping synchronization operations failed because of timeout.')
            );
            $flag->unlock();
        }
        return $this;
    }
}
