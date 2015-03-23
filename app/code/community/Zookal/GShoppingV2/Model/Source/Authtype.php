<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Data Api authorization types Source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Source_Authtype
{
    /**
     * Retrieve option array with authentification types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'authsub', 'label' => Mage::helper('gshoppingv2')->__('AuthSub')],
            ['value' => 'clientlogin', 'label' => Mage::helper('gshoppingv2')->__('ClientLogin')]
        ];
    }
}
