<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Google Content Item Types Mapping grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Block_Adminhtml_Types_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('types_grid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return Zookal_GShoppingV2_Block_Adminhtml_Types_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('gshoppingv2/type_collection')->addItemsCount();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare grid colunms
     *
     * @return Zookal_GShoppingV2_Block_Adminhtml_Types_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('attribute_set_name',
            [
                'header' => $this->__('Attributes Set'),
                'index'  => 'attribute_set_name',
            ]);

        $this->addColumn('target_country',
            [
                'header'   => $this->__('Target Country'),
                'width'    => '150px',
                'index'    => 'target_country',
                'renderer' => 'gshoppingv2/adminhtml_types_renderer_country',
                'filter'   => false
            ]);

        $this->addColumn('items_total',
            [
                'header' => Mage::helper('catalog')->__('Total Qty Content Items'),
                'width'  => '150px',
                'index'  => 'items_total',
                'filter' => false
            ]);

        return parent::_prepareColumns();
    }

    /**
     * Return row url for js event handlers
     *
     * @param Varien_Object
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId(), '_current' => true]);
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
