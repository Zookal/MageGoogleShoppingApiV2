<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax attribute model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Attribute_Tax
    extends Zookal_GShoppingV2_Model_Attribute_Default
{
    /**
     * Maximum number of tax rates per product supported by google shopping api
     */
    const RATES_MAX = 100;

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

        if (Mage::helper('tax')->getConfig()->priceIncludesTax()) {
            return $shoppingProduct;
        }

        $sp = $this->_dispatch('gshoppingv2_attribute_tax', $product, $shoppingProduct);
        if ($sp !== null) {
            return $sp;
        }

        $calc             = Mage::helper('tax')->getCalculator();
        $customerTaxClass = $calc->getDefaultCustomerTaxClass($product->getStoreId());
        $rates            = $calc->getRatesByCustomerAndProductTaxClasses($customerTaxClass, $product->getTaxClassId());
        $targetCountry    = Mage::getSingleton('gshoppingv2/config')->getTargetCountry($product->getStoreId());
        $ratesTotal       = 0;
        $taxes            = [];
        foreach ($rates as $rate) {
            if ($targetCountry == $rate['country']) {
                $regions = $this->_parseRegions($rate['state'], $rate['postcode']);
                $ratesTotal += count($regions);
                if ($ratesTotal > self::RATES_MAX) {
                    Mage::throwException(Mage::helper('gshoppingv2')->__("Google shopping only supports %d tax rates per product", self::RATES_MAX));
                }
                foreach ($regions as $region) {
                    $taxes[] = [
                        'country' => empty($rate['country']) ? '*' : $rate['country'],
                        'region'  => $region,
                        'rate'    => $rate['value'] * 100,
                        'taxShip' => true
                    ];
                }
            }
        }
        $shoppingProduct->setTaxes($taxes);

        return $shoppingProduct;
    }

    /**
     * Retrieve array of regions characterized by provided params
     *
     * @param string $state
     * @param string $zip
     *
     * @return array
     */
    protected function _parseRegions($state, $zip)
    {
        return (!empty($zip) && $zip != '*') ? $this->_parseZip($zip) : (($state) ? [$state] : ['*']);
    }

    /**
     * Retrieve array of regions characterized by provided zip code
     *
     * @param string $zip
     *
     * @return array
     */
    protected function _parseZip($zip)
    {
        if (strpos($zip, '-') == -1) {
            return [$zip];
        } else {
            return Mage::helper('googlecheckout')->zipRangeToZipPattern($zip);
        }
    }
}
