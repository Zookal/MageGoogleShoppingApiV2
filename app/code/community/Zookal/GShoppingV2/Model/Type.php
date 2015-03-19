<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Content Item Types Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/**
 * @method int getTypeId()
 * @method Zookal_GShoppingV2_Model_Type setTypeId(int $value)
 * @method int getAttributeSetId()
 * @method Zookal_GShoppingV2_Model_Type setAttributeSetId(int $value)
 * @method string getTargetCountry()
 * @method Zookal_GShoppingV2_Model_Type setTargetCountry(string $value)
 */
class Zookal_GShoppingV2_Model_Type extends Mage_Core_Model_Abstract
{
    /**
     * Mapping attributes collection
     *
     * @var Zookal_GShoppingV2_Model_Mysql4_Attribute_Collection
     */
    protected $_attributesCollection;

    protected function _construct()
    {
        $this->_init('gshoppingv2/type');
    }

    /**
     * Load type model by Attribute Set Id and Target Country
     *
     * @param int    $attributeSetId Attribute Set
     * @param string $targetCountry  Two-letters country ISO code
     *
     * @return Zookal_GShoppingV2_Model_Type
     */
    public function loadByAttributeSetId($attributeSetId, $targetCountry)
    {
        return $this->getResource()
            ->loadByAttributeSetIdAndTargetCountry($this, $attributeSetId, $targetCountry);
    }

    public function convertAttributes($product)
    {

        $newShoppingProduct = new Google_Service_ShoppingContent_Product();
        $map                = $this->_getAttributesMapByProduct($product);
        $base               = $this->_getBaseAttributes();
        $attributes         = array_merge($base, $map);

        foreach ($attributes as $name => $attribute) {
            Mage::log($name);
            Mage::log(get_class($attribute));
            $attribute->convertAttribute($product, $newShoppingProduct);
        }

        return $newShoppingProduct;
    }

    /**
     * Return Product attribute values array
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return array Product attribute values
     */
    protected function _getAttributesMapByProduct(Mage_Catalog_Model_Product $product)
    {
        $result = [];
        $group  = Mage::getSingleton('gshoppingv2/config')->getAttributeGroupsFlat();
        foreach ($this->_getAttributesCollection() as $attribute) {
            $productAttribute = Mage::helper('gshoppingv2/product')
                ->getProductAttribute($product, $attribute->getAttributeId());

            if (!is_null($productAttribute)) {
                // define final attribute name
                if ($attribute->getGcontentAttribute()) {
                    $name = $attribute->getGcontentAttribute();
                } else {
                    $name = Mage::helper('gshoppingv2/product')->getAttributeLabel($productAttribute, $product->getStoreId());
                }

                if (!is_null($name)) {
                    $name = Mage::helper('gshoppingv2')->normalizeName($name);
                    if (isset($group[$name])) {
                        // if attribute is in the group
                        if (!isset($result[$group[$name]])) {
                            $result[$group[$name]] = $this->_createAttribute($group[$name]);
                        }
                        // add group attribute to parent attribute
                        $result[$group[$name]]->addData([
                            'group_attribute_' . $name => $this->_createAttribute($name)->addData($attribute->getData())
                        ]);
                        unset($group[$name]);
                    } else {
                        if (!isset($result[$name])) {
                            $result[$name] = $this->_createAttribute($name);
                        }
                        $result[$name]->addData($attribute->getData());
                    }
                }
            }
        }

        return $this->_initGroupAttributes($result);
    }

    /**
     * Retrun array with base attributes
     *
     * @return array
     */
    protected function _getBaseAttributes()
    {
        $names      = Mage::getSingleton('gshoppingv2/config')->getBaseAttributes();
        $attributes = [];
        foreach ($names as $name) {
            $attributes[$name] = $this->_createAttribute($name);
        }

        return $this->_initGroupAttributes($attributes);
    }

    /**
     * Append to attributes array subattribute's models
     *
     * @param array $attributes
     *
     * @return array
     */
    protected function _initGroupAttributes($attributes)
    {
        $group = Mage::getSingleton('gshoppingv2/config')->getAttributeGroupsFlat();
        foreach ($group as $child => $parent) {
            if (isset($attributes[$parent]) &&
                !isset($attributes[$parent]['group_attribute_' . $child])
            ) {
                $attributes[$parent]->addData(
                    ['group_attribute_' . $child => $this->_createAttribute($child)]
                );
            }
        }

        return $attributes;
    }

    /**
     * Prepare Google Content attribute model name
     *
     * @param string Attribute name
     *
     * @return string Normalized attribute name
     */
    protected function _prepareModelName($string)
    {
        $string = Mage::helper('gshoppingv2')->normalizeName($string);
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

    /**
     * Create attribute instance using attribute's name
     *
     * @param string $name
     *
     * @return Zookal_GShoppingV2_Model_Attribute
     */
    protected function _createAttribute($name)
    {
        $modelName = 'gshoppingv2/attribute_' . $this->_prepareModelName($name);

        $useDefault = false;
        try {
            $attributeModel = Mage::getModel($modelName);
            $useDefault     = !$attributeModel;
        } catch (Exception $e) {
            $useDefault = true;
        }
        if ($useDefault) {
            $attributeModel = Mage::getModel('gshoppingv2/attribute_default');
        }
        $attributeModel->setName($name);

        return $attributeModel;
    }

    /**
     * Retrieve type's attributes collection
     * It is protected, because only Type knowns about its attributes
     *
     * @return Zookal_GShoppingV2_Model_Mysql4_Attribute_Collection
     */
    protected function _getAttributesCollection()
    {
        if (is_null($this->_attributesCollection)) {
            $this->_attributesCollection = Mage::getResourceModel('gshoppingv2/attribute_collection')
                ->addAttributeSetFilter($this->getAttributeSetId(), $this->getTargetCountry());
        }
        return $this->_attributesCollection;
    }
}
