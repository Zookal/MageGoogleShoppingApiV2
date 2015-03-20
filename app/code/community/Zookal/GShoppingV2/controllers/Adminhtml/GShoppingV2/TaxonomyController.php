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
class Zookal_GShoppingV2_Adminhtml_GShoppingV2_TaxonomyController
    extends Mage_Adminhtml_Controller_Action
{
    const MIN_LENGTH = 3;

    /**
     * Search result grid with available products for Google Content
     */
    public function searchAction()
    {
        $q = $this->getRequest()->getParam('query', '');
        if (strlen($q) < self::MIN_LENGTH) {
            $this->getResponse()->setBody('');
            $this->getResponse()->sendResponse();
            return;
        }

        /** @var Zookal_GShoppingV2_Model_Resource_Taxonomy_Collection $taxonomyResults */
        $taxonomyResults = Mage::getModel('gshoppingv2/taxonomy')->getCollection();
        $taxonomyResults
            ->addLocaleFilter((int)$this->getRequest()->getParam('store', 0))
            ->searchByName($q);

        $block = $this->getLayout()->createBlock('adminhtml/template')
            ->setTemplate('gshoppingv2/autocomplete.phtml')
            ->assign('items', $taxonomyResults);

        $this->getResponse()->setBody($block->toHtml());
    }
}
