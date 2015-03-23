<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Content Item statues Source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Source_Statuses
{
    /**
     * Retrieve option array with Google Content item's statuses
     *
     * @return array
     */
    public function getStatuses()
    {
        return [
            '0' => Mage::helper('gshoppingv2')->__('Yes'),
            '1' => Mage::helper('gshoppingv2')->__('No')
        ];
    }
}
