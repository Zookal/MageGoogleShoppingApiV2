<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Controller for mass opertions with items
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_MassOperations
{
    /**
     * Zend_Db_Statement_Exception code for "Duplicate unique index" error
     *
     * @var int
     */
    const ERROR_CODE_SQL_UNIQUE_INDEX = 23000;

    /**
     * Whether general error information were added
     *
     * @var bool
     */
    protected $_hasError = false;

    /**
     * Process locking flag
     *
     * @var Zookal_GShoppingV2_Model_Flag
     */
    protected $_flag;

    /**
     * Set process locking flag.
     *
     * @param Zookal_GShoppingV2_Model_Flag $flag
     *
     * @return Zookal_GShoppingV2_Model_MassOperations
     */
    public function setFlag(Zookal_GShoppingV2_Model_Flag $flag)
    {
        $this->_flag = $flag;
        return $this;
    }

    /**
     * Add product to Google Content.
     *
     * @param array $productIds
     * @param int   $storeId
     *
     * @throws Zend_Gdata_App_CaptchaRequiredException
     * @throws Mage_Core_Exception
     * @return Zookal_GShoppingV2_Model_MassOperations
     */
    public function addProducts($productIds, $storeId)
    {

        $totalAdded = 0;
        $errors     = [];
        if (is_array($productIds)) {
            foreach ($productIds as $productId) {
                if ($this->_flag && $this->_flag->isExpired()) {
                    break;
                }
                try {
                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($storeId)
                        ->load($productId);

                    if ($product->getId()) {
                        Mage::getModel('gshoppingv2/item')
                            ->insertItem($product)
                            ->save();
                        // The product was added successfully
                        $totalAdded++;
                    }
                } catch (Mage_Core_Exception $e) {
                    $errors[] = Mage::helper('gshoppingv2')->__('The product "%s" cannot be added to Google Content. %s', $product->getName(), $e->getMessage());
                } catch (Exception $e) {
                    Mage::logException($e);
                    $errors[] = Mage::helper('gshoppingv2')->__('The product "%s" hasn\'t been added to Google Content.', $product->getName());
                    $errors[] = $e->getMessage();
                }
            }
            if (empty($productIds)) {
                return $this;
            }
        }

        if ($totalAdded > 0) {
            $this->_getNotifier()->addNotice(
                Mage::helper('gshoppingv2')->__('Products were added to Google Shopping account.'),
                Mage::helper('gshoppingv2')->__('Total of %d product(s) have been added to Google Content.', $totalAdded)
            );
        }

        if (count($errors)) {
            $this->_getNotifier()->addMajor(
                Mage::helper('gshoppingv2')->__('Errors happened while adding products to Google Shopping.'),
                $errors
            );
        }

        if ($this->_flag->isExpired()) {
            $this->_getNotifier()->addMajor(
                Mage::helper('gshoppingv2')->__('Operation of adding products to Google Shopping expired.'),
                Mage::helper('gshoppingv2')->__('Some products may have not been added to Google Shopping bacause of expiration')
            );
        }

        return $this;
    }

    /**
     * Update Google Content items.
     *
     * @param array|Zookal_GShoppingV2_Model_Resource_Item_Collection $items
     *
     * @throws Zend_Gdata_App_CaptchaRequiredException
     * @throws Mage_Core_Exception
     * @return Zookal_GShoppingV2_Model_MassOperations
     */
    public function synchronizeItems($items)
    {
        $totalUpdated = 0;
        $totalDeleted = 0;
        $totalFailed  = 0;
        $errors       = [];

        $itemsCollection = $this->_getItemsCollection($items);

        if ($itemsCollection) {
            if (count($itemsCollection) < 1) {
                return $this;
            }
            foreach ($itemsCollection as $item) {
                if ($this->_flag && $this->_flag->isExpired()) {
                    break;
                }
                $removeInactive = $this->_getConfig()->getConfigData('autoremove_disabled', $item->getStoreId());
                $renewNotListed = $this->_getConfig()->getConfigData('autorenew_notlisted', $item->getStoreId());
                try {
                    if ($removeInactive && ($item->getProduct()->isDisabled() || !$item->getProduct()->getStockItem()->getIsInStock())) {
                        $item->deleteItem();
                        $item->delete();
                        $totalDeleted++;
                        Mage::log("remove inactive: " . $item->getProduct()->getSku() . " - " . $item->getProduct()->getName());
                    } else {
                        $item->updateItem();
                        $item->save();
                        // The item was updated successfully
                        $totalUpdated++;
                    }
                } catch (Mage_Core_Exception $e) {
                    $errors[] = Mage::helper('gshoppingv2')->__('The item "%s" cannot be updated at Google Content. %s', $item->getProduct()->getName(), $e->getMessage());
                    $totalFailed++;
                } catch (Exception $e) {
                    Mage::logException($e);
                    $errors[] = Mage::helper('gshoppingv2')->__('The item "%s" hasn\'t been updated.', $item->getProduct()->getName());
                    $errors[] = $e->getMessage();
                    $totalFailed++;
                }
            }
        } else {
            return $this;
        }

        $this->_getNotifier()->addNotice(
            Mage::helper('gshoppingv2')->__('Product synchronization with Google Shopping completed'),
            Mage::helper('gshoppingv2')->__('Total of %d items(s) have been deleted; total of %d items(s) have been updated.', $totalDeleted, $totalUpdated)
        );
        if ($totalFailed > 0 || count($errors)) {
            array_unshift($errors, Mage::helper('gshoppingv2')->__("Cannot update %s items.", $totalFailed));
            $this->_getNotifier()->addMajor(
                Mage::helper('gshoppingv2')->__('Errors happened during synchronization with Google Shopping'),
                $errors
            );
        }

        return $this;
    }

    /**
     * Remove Google Content items.
     *
     * @param array|Zookal_GShoppingV2_Model_Resource_Item_Collection $items
     *
     * @return Zookal_GShoppingV2_Model_MassOperations
     */
    public function deleteItems($items)
    {
        $totalDeleted    = 0;
        $itemsCollection = $this->_getItemsCollection($items);
        $errors          = [];
        if ($itemsCollection) {
            if (count($itemsCollection) < 1) {
                return $this;
            }
            foreach ($itemsCollection as $item) {
                if ($this->_flag && $this->_flag->isExpired()) {
                    break;
                }
                try {
                    $item->deleteItem()->delete();
                    // The item was removed successfully
                    $totalDeleted++;
                } catch (Exception $e) {
                    Mage::logException($e);
                    $errors[] = Mage::helper('gshoppingv2')->__('The item "%s" hasn\'t been deleted.', $item->getProduct()->getName());
                }
            }
        } else {
            return $this;
        }

        if ($totalDeleted > 0) {
            $this->_getNotifier()->addNotice(
                Mage::helper('gshoppingv2')->__('Google Shopping item removal process succeded'),
                Mage::helper('gshoppingv2')->__('Total of %d items(s) have been removed from Google Shopping.', $totalDeleted)
            );
        }
        if (count($errors)) {
            $this->_getNotifier()->addMajor(
                Mage::helper('gshoppingv2')->__('Errors happened while deleting items from Google Shopping'),
                $errors
            );
        }

        return $this;
    }

    /**
     * Return items collection by IDs
     *
     * @param array|Zookal_GShoppingV2_Model_Resource_Item_Collection $items
     *
     * @throws Mage_Core_Exception
     * @return null|Zookal_GShoppingV2_Model_Resource_Item_Collection
     */
    protected function _getItemsCollection($items)
    {
        $itemsCollection = null;
        if ($items instanceof Zookal_GShoppingV2_Model_Resource_Item_Collection) {
            $itemsCollection = $items;
        } else if (is_array($items)) {
            $itemsCollection = Mage::getResourceModel('gshoppingv2/item_collection')
                ->addFieldToFilter('item_id', $items);
        }

        return $itemsCollection;
    }

    /**
     * Retrieve adminhtml session model object
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Retrieve admin notifier
     *
     * @return Mage_Adminhtml_Model_Inbox
     */
    protected function _getNotifier()
    {
        return Mage::getModel('adminnotification/inbox');
    }

    /**
     * Provides general error information
     */
    protected function _addGeneralError()
    {
        if (!$this->_hasError) {
            $this->_getNotifier()->addMajor(
                Mage::helper('gshoppingv2')->__('Google Shopping Error'),
                Mage::helper('gshoppingv2/category')->getMessage()
            );
            $this->_hasError = true;
        }
    }

    /**
     * Get Google Shopping config model
     *
     * @return Mage_GoogleShopping_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('gshoppingv2/config');
    }
}
