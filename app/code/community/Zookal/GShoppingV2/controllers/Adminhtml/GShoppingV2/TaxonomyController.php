<?php
/**
 * NOTICE OF LICENSE
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright  Copyright (c) Zookal Services Pte Ltd
 * @author     Cyrill Schumacher @schumacherfm
 * @license    See LICENSE.txt
 */

/**
 * GoogleShopping Products selection grid controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Adminhtml_GShoppingV2_SelectionController
    extends Mage_Adminhtml_Controller_Action
{
    const MIN_LENGTH = 3;

    /**
     * Search result grid with available products for Google Content
     */
    public function searchAction()
    {
        $q = $this->getRequest()->getParam('q', '');
        if (strlen($q) < self::MIN_LENGTH) {
            $this->getResponse()->setBody('');
            $this->getResponse()->sendResponse();
            return;
        }

        $taxonomyResults = Mage::getModel('gshoppingv2/taxonomy')->getCollection();
        $taxonomyResults->addFieldToFilter('name', ['like' => '%' . $q . '%']);

        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('gshoppingv2/adminhtml_items_product')
                ->setIndex($this->getRequest()->getParam('index'))
                ->setFirstShow(true)
                ->toHtml()
        );
    }
}
