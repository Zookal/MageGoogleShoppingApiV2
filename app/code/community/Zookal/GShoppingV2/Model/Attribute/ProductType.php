<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ProductType attribute model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Attribute_ProductType
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
        $sp = $this->_dispatch('gshoppingv2_attribute_producttype', $product, $shoppingProduct);
        if ($sp !== null) {
            return $sp;
        }

        $productCategories = $product->getCategoryIds();

        // TODO: set Default value for product_type attribute if product isn't assigned for any category
        $value = 'Shop';

        if (!empty($productCategories)) {
            $category = Mage::getModel('catalog/category')->load(
                array_shift($productCategories)
            );

            $breadcrumbs = [];

            foreach ($category->getParentCategories() as $cat) {
                $breadcrumbs[] = $cat->getName();
            }

            $value = implode(' > ', $breadcrumbs);
        }

        $shoppingProduct->setProductType($value);
        return $shoppingProduct;
    }
}
