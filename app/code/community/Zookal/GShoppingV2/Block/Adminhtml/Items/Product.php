<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Products Grid to add to Google Content
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Block_Adminhtml_Items_Product
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gShoppingV2_selection_search_grid');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return Zookal_GShoppingV2_Block_Adminhtml_Items_Product
     */
    protected function _beforeToHtml()
    {
        $this->setId($this->getId() . '_' . $this->getIndex());
        $this->getChild('reset_filter_button')->setData('onclick', $this->getJsObjectName() . '.resetFilter()');
        $this->getChild('search_button')->setData('onclick', $this->getJsObjectName() . '.doFilter()');
        return parent::_beforeToHtml();
    }

    /**
     * Prepare grid collection object
     *
     * @return Zookal_GShoppingV2_Block_Adminhtml_Items_Product
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->setStore($this->_getStore())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('attribute_set_id');

        $store = $this->_getStore();
        if ($store->getId()) {
            $collection->addStoreFilter($store);
        }

        $excludeIds = $this->_getGoogleShoppingProductIds();
        if ($excludeIds) {
            $collection->addIdFilter($excludeIds, true);
        }

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        Mage::dispatchEvent('gshoppingv2_block_adminhtml_items_product_collection', [
            'collection' => $collection,
        ]);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Zookal_GShoppingV2_Block_Adminhtml_Items_Product
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', [
            'header'   => Mage::helper('sales')->__('ID'),
            'sortable' => true,
            'width'    => '60px',
            'index'    => 'entity_id'
        ]);
        $this->addColumn('name', [
            'header'           => Mage::helper('sales')->__('Product Name'),
            'index'            => 'name',
            'column_css_class' => 'name'
        ]);

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('type',
            [
                'header'  => Mage::helper('catalog')->__('Type'),
                'width'   => '60px',
                'index'   => 'type_id',
                'type'    => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ]);

        $this->addColumn('set_name',
            [
                'header'  => Mage::helper('catalog')->__('Attrib. Set Name'),
                'width'   => '100px',
                'index'   => 'attribute_set_id',
                'type'    => 'options',
                'options' => $sets,
            ]);

        $this->addColumn('sku', [
            'header'           => Mage::helper('sales')->__('SKU'),
            'width'            => '80px',
            'index'            => 'sku',
            'column_css_class' => 'sku'
        ]);

        $this->addColumn('price', [
            'header'        => Mage::helper('sales')->__('Price'),
            'align'         => 'center',
            'type'          => 'currency',
            'currency_code' => $this->_getStore()->getDefaultCurrencyCode(),
            'rate'          => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getDefaultCurrencyCode()),
            'index'         => 'price'
        ]);

        $this->addColumn('status', [
            'header'           => Mage::helper('sales')->__('Status'),
            'width'            => '80px',
            'index'            => 'status',
            'type'             => 'options',
            'options'          => [
                Mage_Catalog_Model_Product_Status::STATUS_ENABLED  => Mage::helper('sales')->__('Enabled'),
                Mage_Catalog_Model_Product_Status::STATUS_DISABLED => Mage::helper('sales')->__('Disabled')
            ],
            'column_css_class' => 'status'
        ]);

        Mage::dispatchEvent('gshoppingv2_block_adminhtml_items_product_grid', [
            'grid' => $this,
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid massaction actions
     *
     * @return Zookal_GShoppingV2_Block_Adminhtml_Items_Product
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('add', [
            'label' => $this->__('Add to Google Content'),
            'url'   => $this->getUrl('*/*/massAdd', ['_current' => true]),
        ]);
        return $this;
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/gShoppingV2_selection/grid', ['index' => $this->getIndex(), '_current' => true]);
    }

    /**
     * Disable clickable row
     *
     * @param $item
     *
     * @return bool
     */
    public function getRowUrl($item)
    {
        return false;
    }

    /**
     * Get array with product ids, which was exported to Google Content
     *
     * @return array
     */
    protected function _getGoogleShoppingProductIds()
    {
        $collection = Mage::getResourceModel('gshoppingv2/item_collection')
            ->addStoreFilter($this->_getStore()->getId())
            ->load();
        $productIds = [];
        foreach ($collection as $item) {
            $productIds[] = $item->getProductId();
        }
        return $productIds;
    }

    /**
     * Get store model by request param
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        return Mage::app()->getStore($this->getRequest()->getParam('store'));
    }
}
