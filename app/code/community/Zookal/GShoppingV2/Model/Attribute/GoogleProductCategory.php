<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * GoogleProductCategory attribute model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Attribute_GoogleProductCategory
    extends Zookal_GShoppingV2_Model_Attribute_Default
{
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
        $sp = $this->_dispatch('gshoppingv2_attribute_googleproductcategory', $product,
            $shoppingProduct);
        if ($sp !== null) {
            return $sp;
        }

        // get category from product attribute
        $value = $product->getResource()->getAttribute('google_shopping_category')->getFrontend()->getValue($product);
        $value = preg_replace('/\d+ /', '', $value);
        $shoppingProduct->setGoogleProductCategory($value);

        return $shoppingProduct;
    }
}
