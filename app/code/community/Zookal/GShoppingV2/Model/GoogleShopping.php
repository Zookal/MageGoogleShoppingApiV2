<?php
/**
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Shopping connector
 *
 */
class Zookal_GShoppingV2_Model_GoogleShopping extends Varien_Object
{

    const APPNAME = 'Magento GoogleShopping V2';

    /**
     * @var Google_Client
     */
    protected $_client = null;

    /**
     * @var Google_Service_ShoppingContent
     */
    protected $_shoppingService = null;

    /**
     * Google Content Config
     *
     * @return Zookal_GShoppingV2_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('gshoppingv2/config');
    }

    /**
     * Redirect to OAuth2 authentication
     *
     * @param int  $storeId
     * @param bool $noAuthRedirect
     *
     * @return bool
     */
    public function redirectToAuth($storeId, $noAuthRedirect)
    {
        if ($noAuthRedirect === true) {
            return false;
        } else {
            $url = Mage::getUrl("adminhtml/gShoppingV2_oauth/auth", ['store_id' => $storeId]);
            header('Location: ' . $url . "\n");
            echo '<html><head><meta http-equiv="refresh" content="0;URL=\'' . $url . '\'" />';
            echo '</head><body><a href="' . $url . '">' . $url . '</a></body></html>';
            exit;
        }
    }

    /**
     * Check if client is authenticated for storeId
     *
     * @param int $storeId
     *
     * @return bool
     */
    public function isAuthenticated($storeId)
    {
        return !($this->getClient($storeId, true) === false);
    }

    /**
     * @param int $storeId
     *
     * @return bool
     */
    private function _getAccessToken($storeId)
    {
        $clientId     = $this->getConfig()->getConfigData('client_id', $storeId);
        $accessTokens = Mage::getSingleton('admin/session')->getGoogleOAuth2Token();
        return isset($accessTokens[$clientId]) && false === empty($accessTokens[$clientId])
            ? $accessTokens[$clientId]
            : false;
    }

    /**
     * Return Google Content Client Instance
     *
     * @param int  $storeId
     * @param bool $noAuthRedirect
     *
     * @return bool|Google_Client
     */
    public function getClient($storeId, $noAuthRedirect = false)
    {

        if (isset($this->_client)) {
            if ($this->_client->isAccessTokenExpired()) {
                return $this->redirectToAuth($storeId, $noAuthRedirect);
            }
            return $this->_client;
        }
        $clientId     = $this->getConfig()->getConfigData('client_id', $storeId);
        $clientSecret = $this->getConfig()->getClientSecret($storeId);
        $accessToken  = $this->_getAccessToken($storeId);

        if (!$clientId || !$clientSecret) {
            Mage::getSingleton('adminhtml/session')->addError("Please specify Google Content API access data for this store!");
            return false;
        }

        if (!isset($accessToken) || empty($accessToken)) {
            return $this->redirectToAuth($storeId, $noAuthRedirect);
        }

        $this->_client = new Google_Client();
        $this->_client->setApplicationName(self::APPNAME);
        $this->_client->setClientId($clientId);
        $this->_client->setClientSecret($clientSecret);
        $this->_client->setScopes('https://www.googleapis.com/auth/content');
        $this->_client->setAccessToken($accessToken);

        if ($this->_client->isAccessTokenExpired()) {
            return $this->redirectToAuth($storeId, $noAuthRedirect);
        }

        if ($this->getConfig()->getIsDebug($storeId)) {
            $this->_client->setLogger(Mage::getModel('gshoppingv2/logger', $this->_client)->setStoreID($storeId));
        }
        return $this->_client;
    }

    /**
     * @param null|int $storeId
     *
     * @return Google_Service_ShoppingContent
     */
    public function getShoppingService($storeId = null)
    {
        if (null !== $this->_shoppingService) {
            return $this->_shoppingService;
        }

        $this->_shoppingService = new Google_Service_ShoppingContent($this->getClient($storeId));
        return $this->_shoppingService;
    }

    /**
     * @param null|int $storeId
     *
     * @return Google_Service_ShoppingContent_ProductsListResponse
     */
    public function listProducts($storeId = null)
    {
        $merchantId = $this->getConfig()->getConfigData('merchant_id', $storeId);
        return $this->getShoppingService($storeId)->products->listProducts($merchantId);
    }

    /**
     * @param  string  $productId
     * @param null|int $storeId
     *
     * @return Google_Service_ShoppingContent_Product
     */
    public function getProduct($productId, $storeId = null)
    {
        $merchantId = $this->getConfig()->getConfigData('account_id', $storeId);
        return $this->getShoppingService($storeId)->products->get($merchantId, $productId);
    }

    /**
     * @param string   $productId
     * @param null|int $storeId
     *
     * @return Google_Http_Request
     */
    public function deleteProduct($productId, $storeId = null)
    {
        $merchantId = $this->getConfig()->getConfigData('account_id', $storeId);
        $result     = $this->getShoppingService($storeId)->products->delete($merchantId, $productId);
        return $result;
    }

    /**
     * @param Google_Service_ShoppingContent_Product $product
     * @param null|int                               $storeId
     *
     * @return Google_Service_ShoppingContent_Product
     */
    public function insertProduct(Google_Service_ShoppingContent_Product $product, $storeId = null)
    {
        $merchantId = $this->getConfig()->getConfigData('account_id', $storeId);
        $product->setChannel("online");
        $expDate = date("Y-m-d", (time() + 30 * 24 * 60 * 60));//product expires in 30 days
        $product->setExpirationDate($expDate);
        $result = $this->getShoppingService($storeId)->products->insert($merchantId, $product);
        return $result;
    }

    /**
     * @param Google_Service_ShoppingContent_Product $product
     * @param null|int                               $storeId
     *
     * @return Google_Service_ShoppingContent_Product
     */
    public function updateProduct(Google_Service_ShoppingContent_Product $product, $storeId = null)
    {
        return $this->insertProduct($product, $storeId);
    }
}
