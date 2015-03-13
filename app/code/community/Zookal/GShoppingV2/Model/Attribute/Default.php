<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Default attribute model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Attribute_Default
    extends Zookal_GShoppingV2_Model_Attribute
{
    /**
     * Google Content attribute types
     *
     * @var string
     */
    const ATTRIBUTE_TYPE_TEXT = 'text';
    const ATTRIBUTE_TYPE_INT = 'int';
    const ATTRIBUTE_TYPE_FLOAT = 'float';
    const ATTRIBUTE_TYPE_URL = 'url';

    /**
     * @param string                                 $name
     * @param Mage_Catalog_Model_Product             $product
     * @param Google_Service_ShoppingContent_Product $shoppingProduct
     *
     * @return null|Google_Service_ShoppingContent_Product
     */
    protected function _dispatch(
        $name,
        Mage_Catalog_Model_Product $product,
        Google_Service_ShoppingContent_Product $shoppingProduct
    )
    {
        $dispatched = new Varien_Object([
            'has_changes' => false
        ]);

        Mage::dispatchEvent($name, [
            'attribute'       => $this,
            'product'         => $product,
            'shoppingProduct' => $shoppingProduct,
            'dispatched'      => $dispatched,
        ]);

        if ($dispatched->getHasChanges() === true) {
            return $shoppingProduct;
        }
        return null;
    }

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
        $sp = $this->_dispatch('gshoppingv2_attribute_default', $product, $shoppingProduct);
        if ($sp !== null) {
            return $sp;
        }

        if (is_null($this->getName())) {
            return $shoppingProduct;
        }
        $productAttribute = Mage::helper('gshoppingv2/product')
            ->getProductAttribute($product, $this->getAttributeId());
        $type             = $this->getGcontentAttributeType($productAttribute);
        $value            = $this->getProductAttributeValue($product);

        if (!is_null($value)) {
            $shoppingProduct->offsetSet($this->getName(), $value);
        }

        return $shoppingProduct;
    }

    /**
     * Get current attribute value for specified product
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return null|string
     */
    public function getProductAttributeValue($product)
    {
        if (is_null($this->getAttributeId())) {
            return null;
        }

        $productAttribute = Mage::helper('gshoppingv2/product')
            ->getProductAttribute($product, $this->getAttributeId());
        if (is_null($productAttribute)) {
            return null;
        }

        if ($productAttribute->getFrontendInput() == 'date' ||
            $productAttribute->getBackendType() == 'date'
        ) {
            $value = $product->getData($productAttribute->getAttributeCode());
            if (empty($value) || !Zend_Date::isDate($value, Zend_Date::ISO_8601)) {
                return null;
            }
            $date  = new Zend_Date($value, Zend_Date::ISO_8601);
            $value = $date->toString(Zend_Date::ATOM);
        } else {
            $value = $productAttribute->getFrontend()->getValue($product);
        }
        return $value;
    }

    /**
     * Return Google Content Attribute Type By Product Attribute
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     *
     * @return string Google Content Attribute Type
     */
    public function getGcontentAttributeType($attribute)
    {
        $typesMapping = [
            'price'   => self::ATTRIBUTE_TYPE_FLOAT,
            'decimal' => self::ATTRIBUTE_TYPE_INT,
        ];
        if (isset($typesMapping[$attribute->getFrontendInput()])) {
            return $typesMapping[$attribute->getFrontendInput()];
        } elseif (isset($typesMapping[$attribute->getBackendType()])) {
            return $typesMapping[$attribute->getBackendType()];
        } else {
            return self::ATTRIBUTE_TYPE_TEXT;
        }
    }
}
