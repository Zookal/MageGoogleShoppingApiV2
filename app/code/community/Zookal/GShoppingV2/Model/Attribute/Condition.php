<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Condition attribute's model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Attribute_Condition
    extends Zookal_GShoppingV2_Model_Attribute_Default
{
    /**
     * Available condition values
     *
     * @var string
     */
    const CONDITION_NEW = 'new';
    const CONDITION_USED = 'used';
    const CONDITION_REFURBISHED = 'refurbished';

    /**
     * Set current attribute to entry (for specified product)
     *
     * @param Mage_Catalog_Model_Product             $product
     * @param Google_Service_ShoppingContent_Product $shoppingProduct
     *
     * @return Google_Service_ShoppingContent_Product
     */
    public function convertAttribute($product, $shoppingProduct)
    {
        $sp = $this->_dispatch('gshoppingv2_attribute_condition', $product, $shoppingProduct);
        if ($sp !== null) {
            return $sp;
        }

        $availableConditions = [
            self::CONDITION_NEW, self::CONDITION_USED, self::CONDITION_REFURBISHED
        ];

        $mapValue = $this->getProductAttributeValue($product);
        if (!is_null($mapValue) && in_array($mapValue, $availableConditions)) {
            $condition = $mapValue;
        } else {
            $condition = self::CONDITION_NEW;
        }

        return $shoppingProduct->setCondition($condition);
    }
}
