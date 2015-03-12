<?php
/**
 * @category    BlueVisionTec
 * @package     BlueVisionTec_GoogleShoppingApi
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml GoogleShopping Store Switcher
 *
 * @category    BlueVisionTec
 * @package     BlueVisionTec_GoogleShoppingApi
 * @author      Magento Core Team <core@magentocommerce.com>
 * @author      BlueVisionTec UG (haftungsbeschränkt) <magedev@bluevisiontec.eu>
 */
class BlueVisionTec_GoogleShoppingApi_Model_Attribute_Source_GoogleShoppingCategories
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @todo move that into DB tables!
     * @todo implement it via AJAX because this slows down the load of catalog/product/edit also terrible implemented
     * :-(
     * implement as a grid system once data is in a table. this also allows multi category selects (if supported by
     * google)
     *
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        $taxonomyPath = Mage::getModuleDir('', 'BlueVisionTec_GoogleShoppingApi') . DS . 'taxonomies' . DS;
        $lang         = Mage::getStoreConfig('general/locale/code', Mage::app()->getRequest()->getParam('store', 0));
        $taxonomyFile = $taxonomyPath . $lang . '.txt';

        if (false === file_exists($taxonomyFile)) {
            $taxonomyFile = $taxonomyPath . 'en_US.txt';
        }

        if (is_null($this->_options)) {
            if(($fh = fopen($taxonomyFile,"r")) !== false) {
                $line = 0;
                $this->_options = array();
                while (($category = fgets($fh)) !== false) {
                    if ($line === 0) {
                        $line++;
                        continue;
                    } // skip first line
                    $line++;
                    $this->_options[] = [
                        'value' => $line,
                        'label' => $line . ' ' . $category
                    ];
                }
            }
        }
        return $this->_options;
    }
}
