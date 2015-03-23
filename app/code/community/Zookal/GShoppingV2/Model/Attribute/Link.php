<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Link attribute model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Attribute_Link
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
        $sp = $this->_dispatch('gshoppingv2_attribute_link', $product, $shoppingProduct);
        if ($sp !== null) {
            return $sp;
        }

        $url = $product->getProductUrl(false);
        if ($url) {
            if (!Mage::getStoreConfigFlag('web/url/use_store')) {
                $urlInfo = parse_url($url);
                $store   = $product->getStore()->getCode();
                if (isset($urlInfo['query']) && $urlInfo['query'] != '') {
                    $url .= '&___store=' . $store;
                } else {
                    $url .= '?___store=' . $store;
                }
            }

            $config = Mage::getSingleton('gshoppingv2/config');
            if ($config->getAddUtmSrcGshopping($product->getStoreId())) {
                $url .= '&utm_source=GoogleShopping';
            }
            if ($customUrlParameters =
                $config->getCustomUrlParameters($product->getStoreId())
            ) {
                $url .= $customUrlParameters;
            }

            $shoppingProduct->setLink($url);
        }

        return $shoppingProduct;
    }
}
