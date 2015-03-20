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
class Zookal_GShoppingV2_Model_Resource_Taxonomy_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('gshoppingv2/taxonomy');
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function searchByName($name)
    {
        $this->addFieldToFilter('name', ['like' => '%' . $name . '%']);
        return $this;
    }

    /**
     * @param int $storeID
     *
     * @return $this
     */
    public function addLocaleFilter($storeID = 0)
    {
        $this->addFieldToFilter('lang', ['like' => $this->_getLanguage($storeID) . '%']);
        return $this;
    }

    /**
     * @param int $storeID
     *
     * @return mixed
     */
    private function _getLanguage($storeID = 0)
    {

        $langRaw = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeID);
        if (empty($langRaw) || strpos($langRaw, '_') === false) {
            $langRaw = Mage_Core_Model_Locale::DEFAULT_LOCALE;
        }
        $langParts = explode('_', $langRaw);
        return $langParts[0];
    }
}
