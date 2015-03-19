<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Content Item resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Resource_Item extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('gshoppingv2/items', 'item_id');
    }

    /**
     * Load Item model by product
     *
     * @param Zookal_GShoppingV2_Model_Item $model
     *
     * @return Zookal_GShoppingV2_Model_Resource_Item
     */
    public function loadByProduct($model)
    {
        if (!($model->getProduct() instanceof Varien_Object)) {
            return $this;
        }

        $product   = $model->getProduct();
        $productId = $product->getId();
        $storeId   = $model->getStoreId() ? $model->getStoreId() : $product->getStoreId();

        $read   = $this->_getReadAdapter();
        $select = $read->select();

        if ($productId !== null) {
            $select->from($this->getMainTable())
                ->where("product_id = ?", $productId)
                ->where('store_id = ?', (int)$storeId);

            $data = $read->fetchRow($select);
            $data = is_array($data) ? $data : [];
            $model->addData($data);
        }
        return $this;
    }
}
