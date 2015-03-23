<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Google Content Item Type Country Renderer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Block_Adminhtml_Types_Renderer_Country
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders Google Content Item Id
     *
     * @param   Varien_Object $row
     *
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $iso = $row->getData($this->getColumn()->getIndex());
        return Mage::getSingleton('gshoppingv2/config')->getCountryInfo($iso, 'name');
    }
}
