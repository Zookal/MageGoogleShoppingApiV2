<?php
/**
 *
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * GoogleShopping install
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
/** @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

if (Mage::helper('gshoppingv2')->isModuleEnabled('Mage_GoogleShopping')) {
    $typesInsert = $installer->getConnection()
        ->select()
        ->from(
            $installer->getTable('googleshopping/types'),
            [
                'type_id',
                'attribute_set_id',
                'target_country',
            ]
        )
        ->insertFromSelect($installer->getTable('gshoppingv2/types'));

    $itemsInsert = $installer->getConnection()
        ->select()
        ->from(
            $installer->getTable('googleshopping/items'),
            [
                'item_id',
                'type_id',
                'product_id',
                'gcontent_item_id',
                'store_id',
                'published',
                'expires'
            ]
        )
        ->insertFromSelect($installer->getTable('gshoppingv2/items'));

    $attributes = '';
    foreach (Mage::getModel('gshoppingv2/config')->getAttributes() as $destAttribtues) {
        foreach ($destAttribtues as $code => $info) {
            $attributes .= "'$code',";
        }
    }
    $attributes       = rtrim($attributes, ',');
    $attributesInsert = $installer->getConnection()
        ->select()
        ->from(
            $installer->getTable('googleshopping/attributes'),
            [
                'id',
                'attribute_id',
                'gcontent_attribute' => new Zend_Db_Expr("IF(gcontent_attribute IN ($attributes), gcontent_attribute, '')"),
                'type_id',
            ]
        )
        ->insertFromSelect($installer->getTable('gshoppingv2/attributes'));

    $installer->run($typesInsert);
    $installer->run($attributesInsert);
    $installer->run($itemsInsert);
}
