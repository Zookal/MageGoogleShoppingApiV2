<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Content language attribute's model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Attribute_ContentLanguage
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
        $sp = $this->_dispatch('gshoppingv2_attribute_contentlanguage', $product, $shoppingProduct);
        if ($sp !== null) {
            return $sp;
        }

        $config        = Mage::getSingleton('gshoppingv2/config');
        $targetCountry = $config->getTargetCountry($product->getStoreId());
        $value         = $config->getCountryInfo($targetCountry, 'language', $product->getStoreId());

        $shoppingProduct->setContentLanguage($value);
    }
}
