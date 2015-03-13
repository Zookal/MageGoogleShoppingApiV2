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
class Zookal_GShoppingV2_Model_Service extends Varien_Object
{

    /**
     * Return Google Content Service Instance
     *
     * @param int $storeId
     *
     * @return Zookal_GShoppingV2_Model_GoogleShopping
     */
    public function getService($storeId = null)
    {
        if (!$this->_service) {
            $this->_service = Mage::getModel('gshoppingv2/googleShopping');

//             if ($this->getConfig()->getIsDebug($storeId)) {
//                 $this->_service
//                     ->setLogAdapter(Mage::getModel('core/log_adapter', 'gshoppingv2.log'), 'log')
//                     ->setDebug(true);
//             }
        }
        return $this->_service;
    }

    /**
     * Set Google Content Service Instance
     *
     * @param Zookal_GShoppingV2_Model_GoogleShopping $service
     *
     * @return Zookal_GShoppingV2_Model_Service
     */
    public function setService($service)
    {
        $this->_service = $service;
        return $this;
    }

    /**
     * Google Content Config
     *
     * @return Zookal_GShoppingV2_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('gshoppingv2/config');
    }
}
