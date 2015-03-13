<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * GoogleShopping Item Types collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Resource_Type_Collection extends
    Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('gshoppingv2/type');
    }

    /**
     * Init collection select
     *
     * @return Zookal_GShoppingV2_Model_Mysql4_Type_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinAttributeSet();
        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $paginatorAdapter = new Zend_Paginator_Adapter_DbSelect($this->getSelect());
        return $paginatorAdapter->getCountSelect();
    }

    /**
     * Add total count of Items for each type
     *
     * @return Zookal_GShoppingV2_Model_Mysql4_Type_Collection
     */
    public function addItemsCount()
    {
        $this->getSelect()
            ->joinLeft(
                ['items' => $this->getTable('gshoppingv2/items')],
                'main_table.type_id=items.type_id',
                ['items_total' => new Zend_Db_Expr('COUNT(items.item_id)')])
            ->group('main_table.type_id');
        return $this;
    }

    /**
     * Add country ISO filter to collection
     *
     * @param string $iso Two-letter country ISO code
     *
     * @return Zookal_GShoppingV2_Model_Mysql4_Type_Collection
     */
    public function addCountryFilter($iso)
    {
        $this->getSelect()->where('target_country=?', $iso);
        return $this;
    }

    /**
     * Join Attribute Set data
     *
     * @return Zookal_GShoppingV2_Model_Mysql4_Type_Collection
     */
    protected function _joinAttributeSet()
    {
        $this->getSelect()
            ->join(
                ['set' => $this->getTable('eav/attribute_set')],
                'main_table.attribute_set_id=set.attribute_set_id',
                ['attribute_set_name' => 'set.attribute_set_name']);
        return $this;
    }
}
