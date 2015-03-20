<?php

/**
 * NOTICE OF LICENSE
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright  Copyright (c) Zookal Services Pte Ltd
 * @author     Cyrill Schumacher @schumacherfm, Chris Zaharia @chrisjz
 * @license    See LICENSE.txt
 */
class Zookal_GShoppingV2_Model_Logger extends Google_Logger_Abstract
{
    /**
     * @var Zookal_GShoppingV2_Model_Config
     */
    private $_config = null;

    private $_storeID = 0;

    /**
     * @param Google_Client $client The current Google client
     */
    public function __construct(Google_Client $client)
    {
        parent::__construct($client);
        $this->_config = Mage::getSingleton('gshoppingv2/config');
    }

    protected function write($message)
    {
        Mage::log($message, null, $this->_config->getLogfile($this->_storeID));
    }

    /**
     * @param int $storeID
     *
     * @return $this
     */
    public function setStoreID($storeID)
    {
        $this->_storeID = (int)$storeID;
        return $this;
    }
}
