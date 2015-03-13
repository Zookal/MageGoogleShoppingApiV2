<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Google Contyent Item Types Grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Block_Adminhtml_Types extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup     = 'gshoppingv2';
        $this->_controller     = 'adminhtml_types';
        $this->_addButtonLabel = Mage::helper('gshoppingv2')->__('Add Attribute Mapping');
        $this->_headerText     = Mage::helper('gshoppingv2')->__('Manage Attribute Mapping');
        parent::__construct();
    }
}
