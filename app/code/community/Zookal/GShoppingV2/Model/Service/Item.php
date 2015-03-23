<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Content Item Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Service_Item extends Varien_Object
{
    /**
     * Insert Item into Google Content
     *
     * @param Zookal_GShoppingV2_Model_Item $item
     *
     * @return Zookal_GShoppingV2_Model_Service_Item
     */
    public function insert($item)
    {
        $product = $item->getType()->convertAttributes($item->getProduct());

        $shoppingProduct = Mage::getSingleton('gshoppingv2/googleShopping')->insertProduct($product, $item->getStoreId());
        $published       = now();

        $item->setGcontentItemId($shoppingProduct->getId())
            ->setPublished($published);

        $expires = $shoppingProduct->getExpirationDate();

        if ($expires) {
            $expires = $this->convertContentDateToTimestamp($expires);
            $item->setExpires($expires);
        }
        return $this;
    }

    /**
     * Update Item data in Google Content
     *
     * @param Zookal_GShoppingV2_Model_Item $item
     *
     * @return Zookal_GShoppingV2_Model_Service_Item
     */
    public function update($item)
    {

        //$gItemId = $item->getGoogleShoppingItemId();

        // get product from google shopping
        //$product = $service->getProduct($gItemId,$item->getStoreId());

        $product = $item->getType()->convertAttributes($item->getProduct());

        $shoppingProduct = Mage::getSingleton('gshoppingv2/googleShopping')->updateProduct($product, $item->getStoreId());

        $expires = $shoppingProduct->getExpirationDate();

        if ($expires) {
            $expires = $this->convertContentDateToTimestamp($expires);
            $item->setExpires($expires);
        }

        return $this;
    }

    /**
     * Delete Item from Google Content
     *
     * @param Zookal_GShoppingV2_Model_Item $item
     *
     * @return Zookal_GShoppingV2_Model_Service_Item
     */
    public function delete($item)
    {
        $gItemId = $item->getGoogleShoppingItemId();
        Mage::getSingleton('gshoppingv2/googleShopping')->deleteProduct($gItemId, $item->getStoreId());
        return $this;
    }

    /**
     * Convert Google Content date format to unix timestamp
     * Ex. 2008-12-08T16:57:23Z -> 2008-12-08 16:57:23
     *
     * @param string Google Content datetime
     *
     * @return int
     */
    public function convertContentDateToTimestamp($gContentDate)
    {
        return Mage::getSingleton('core/date')->date(null, $gContentDate);
    }
}
