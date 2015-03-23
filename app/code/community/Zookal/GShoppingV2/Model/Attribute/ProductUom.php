<?php
/**
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ProductUom attribute model
 *

 */
class Zookal_GShoppingV2_Model_Attribute_ProductUom
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
        $sp = $this->_dispatch('gshoppingv2_attribute_productuom', $product, $shoppingProduct);
        if ($sp !== null) {
            return $sp;
        }

        $availableUnits = [
            'mg', 'g', 'kg',
            'ml', 'cl', 'l', 'cbm',
            'cm', 'm',
            'sqm'
        ];

        $basePriceAmount = $product->getBasePriceAmount();
        $basePriceUnit   = strtolower($product->getBasePriceUnit());

        $unitPricingMeasure = $basePriceAmount . ' ' . $basePriceUnit;

        $basePriceReferenceAmount = $product->getBasePriceBaseAmount();
        $basePriceReferenceUnit   = strtolower($product->getBasePriceBaseUnit());

        $unitPricingBaseMeasure = $basePriceReferenceAmount . ' ' . $basePriceReferenceUnit;

        // skip attribute if unit not available
        if (!in_array($basePriceUnit, $availableUnits) || !in_array($basePriceReferenceUnit, $availableUnits)) {
            return $shoppingProduct;
        }

        if (!empty($basePriceAmount) && !empty($basePriceReferenceAmount)) {

            $unitPricingMeasure = new Google_Service_ShoppingContent_ProductUnitPricingMeasure();
            $unitPricingMeasure->setUnit($basePriceUnit);
            $unitPricingMeasure->setValue($basePriceAmount);
            $unitPricingBaseMeasure = new Google_Service_ShoppingContent_ProductUnitPricingBaseMeasure();
            $unitPricingBaseMeasure->setUnit($basePriceReferenceUnit);
            $unitPricingBaseMeasure->setValue($basePriceReferenceAmount);

            $shoppingProduct->setUnitPricingMeasure($unitPricingMeasure);
            $shoppingProduct->setUnitPricingBaseMeasure($unitPricingBaseMeasure);
        }

        return $shoppingProduct;
    }
}
