<?php

/** @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();

$table = $connection->newTable($this->getTable('gshoppingv2/types'))
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true
    ], 'Type ID')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned' => true,
        'nullable' => false
    ], 'Attribute Set Id')
    ->addColumn('target_country', Varien_Db_Ddl_Table::TYPE_TEXT, 2, [
        'nullable' => false,
        'default'  => 'DE'
    ], 'Target country')
    ->addForeignKey(
        $installer->getFkName(
            'gshoppingv2/types',
            'attribute_set_id',
            'eav/attribute_set',
            'attribute_set_id'
        ),
        'attribute_set_id',
        $this->getTable('eav/attribute_set'),
        'attribute_set_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addIndex(
        $installer->getIdxName(
            'gshoppingv2/types',
            ['attribute_set_id', 'target_country'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['attribute_set_id', 'target_country'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE])
    ->setComment('Google Content Item Types link Attribute Sets');
$installer->getConnection()->createTable($table);

$table = $connection->newTable($this->getTable('gshoppingv2/items'))
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity' => true,
        'nullable' => false,
        'unsigned' => true,
        'primary'  => true
    ], 'Item Id')
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable' => false,
        'unsigned' => true,
        'default'  => 0
    ], 'Type Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable' => false,
        'unsigned' => true
    ], 'Product Id')
    ->addColumn('gcontent_item_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable' => false
    ], 'Google Content Item Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable' => false,
        'unsigned' => true
    ], 'Store Id')
    ->addColumn('published', Varien_Db_Ddl_Table::TYPE_DATETIME, null, [], 'Published date')
    ->addColumn('expires', Varien_Db_Ddl_Table::TYPE_DATETIME, null, [], 'Expires date')
    ->addForeignKey(
        $installer->getFkName(
            'gshoppingv2/items',
            'product_id',
            'catalog/product',
            'entity_id'
        ),
        'product_id',
        $this->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'gshoppingv2/items',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $this->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addIndex($installer->getIdxName('gshoppingv2/items', ['product_id', 'store_id']),
        ['product_id', 'store_id'])
    ->setComment('Google Content Items Products');
$installer->getConnection()->createTable($table);

$table = $connection->newTable($this->getTable('gshoppingv2/attributes'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, [
        'identity' => true,
        'nullable' => false,
        'unsigned' => true,
        'primary'  => true
    ], 'Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable' => false,
        'unsigned' => true
    ], 'Attribute Id')
    ->addColumn('gcontent_attribute', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable' => false
    ], 'Google Content Attribute')
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable' => false,
        'unsigned' => true
    ], 'Type Id')
    ->addForeignKey(
        $installer->getFkName(
            'gshoppingv2/attributes',
            'attribute_id',
            'eav/attribute',
            'attribute_id'
        ),
        'attribute_id',
        $this->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'gshoppingv2/attributes',
            'type_id',
            'gshoppingv2/types',
            'type_id'
        ),
        'type_id',
        $this->getTable('gshoppingv2/types'),
        'type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Google Content Attributes link Product Attributes');
$installer->getConnection()->createTable($table);

$catalogEavSetup         = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrGoogleShoppingImage = $catalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'google_shopping_image');
if ($attrGoogleShoppingImage === false) {
    $catalogEavSetup->addAttribute('catalog_product', 'google_shopping_image',
        [
            'group'    => 'Images',
            'type'     => 'varchar',
            'frontend' => 'catalog/product_attribute_frontend_image',
            'label'    => 'Google Shopping Image',
            'input'    => 'media_image',
            'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            'visible'  => true,
            'default'  => '',
            'class'    => '',
            'source'   => ''
        ]
    );
}
$catalogEavSetup->addAttribute('catalog_product', 'google_shopping_category',
    [
        'group'        => 'Google Shopping',
        'type'         => 'varchar',
        'frontend'     => '',
        'label'        => 'Google Shopping Category',
        'input'        => 'select',
        'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'      => true,
        'default'      => '',
        'class'        => '',
        'source'       => 'gshoppingv2/attribute_source_googleShoppingCategories',
        'required'     => false,
        'user_defined' => true,
    ]
);
$installer->endSetup();

