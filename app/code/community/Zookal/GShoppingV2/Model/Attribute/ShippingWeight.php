<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sipping weight attribute model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Attribute_ShippingWeight
    extends Zookal_GShoppingV2_Model_Attribute_Default
{
    /**
     * Default weight unit
     *
     * @var string
     */
    const WEIGHT_UNIT = 'kg';

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
        $sp = $this->_dispatch('gshoppingv2_attribute_shippingweight', $product, $shoppingProduct);
        if ($sp !== null) {
            return $sp;
        }

        $mapValue = $this->getProductAttributeValue($product);
        if (!$mapValue) {
            $weight   = $this->getGroupAttributeWeight();
            $mapValue = $weight ? $weight->getProductAttributeValue($product) : null;
        }

        if ($mapValue) {
            $shippingWeight = new Google_Service_ShoppingContent_ProductShippingWeight();
            $shippingWeight->setValue($mapValue);
            $shippingWeight->setUnit(self::WEIGHT_UNIT);
            $shoppingProduct->setShippingWeight($shippingWeight);
        }

        return $shoppingProduct;
    }
}
