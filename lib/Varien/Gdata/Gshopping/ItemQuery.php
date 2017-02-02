<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Varien
 * @package     Varien_Gdata
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Item query
 *
 * @category    Varien
 * @package     Varien_Gdata
 */
class Varien_Gdata_Gshopping_ItemQuery extends Zend_Gdata_Query
{
    /**
     * The ID of an item
     *
     * @var string
     */
    protected $_id;

    /**
     * Content language code (ISO 639-1)
     *
     * @var string
     */
    protected $_language;

    /**
     * Target country code (ISO 3166)
     *
     * @var string
     */
    protected $_targetCountry;

    /**
     * @param string $value
     *
     * @return Zend_Gdata_Gbase_ItemQuery Provides a fluent interface
     */
    public function setId($value)
    {
        $this->_id = $value;
        return $this;
    }

    /**
     * Get item's ID
     *
     * @return string id
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set language code
     *
     * @param string $language code
     *
     * @return Varien_Gdata_Gshopping_ItemQuery
     */
    public function setLanguage($language)
    {
        $this->_language = $language;
        return $this;
    }

    /**
     * Get language code
     *
     * @return string code
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * Set target country code
     *
     * @param string $targetCountry code
     *
     * @return Varien_Gdata_Gshopping_ItemQuery
     */
    public function setTargetCountry($targetCountry)
    {
        $this->_targetCountry = $targetCountry;
        return $this;
    }

    /**
     * Get target country code
     *
     * @return string code
     */
    public function getTargetCountry()
    {
        return $this->_targetCountry;
    }

    /**
     * Set default feed's URI
     *
     * @param string $uri URI
     *
     * @return Varien_Gdata_Gshopping_ItemQuery
     */
    public function setFeedUri($uri)
    {
        $this->_defaultFeedUri = $uri;
        return $this;
    }

    /**
     * Returns the query URL generated by this query instance.
     *
     * @return string The query URL for this instance.
     */
    public function getQueryUrl()
    {
        $uri    = $this->_defaultFeedUri;
        $itemId = $this->_getItemId();

        return ($itemId !== null) ? "$uri/$itemId" : $uri . $this->getQueryString();
    }

    /**
     * Build item ID string (with country and language) for URL.
     *
     * @return null|string
     */
    protected function _getItemId()
    {
        return ($this->_targetCountry !== null && $this->_language !== null && $this->_id !== null)
            ? "online:$this->_language:$this->_targetCountry:$this->_id"
            : null;
    }
}
