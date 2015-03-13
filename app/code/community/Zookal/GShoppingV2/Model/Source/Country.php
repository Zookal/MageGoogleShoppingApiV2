<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Content Target country Source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Source_Country
{
    /**
     * Retrieve option array with allowed countries
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_allowed = Mage::getSingleton('gshoppingv2/config')->getAllowedCountries();
        $result   = [];
        foreach ($_allowed as $iso => $info) {
            $result[] = ['value' => $iso, 'label' => $info['name']];
        }
        return $result;
    }
}
