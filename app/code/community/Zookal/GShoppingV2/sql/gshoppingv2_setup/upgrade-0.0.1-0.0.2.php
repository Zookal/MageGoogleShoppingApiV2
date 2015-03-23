<?php
/** @var $installer Zookal_GShoppingV2_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/** @var Magento_Db_Adapter_Pdo_Mysql $connection */
$connection = $installer->getConnection();

$table = $connection->newTable($this->getTable('gshoppingv2/taxonomies'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true
    ], 'ID')
    ->addColumn('lang_idx', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => false
    ], 'Internal Language Index')
    ->addColumn('lang', Varien_Db_Ddl_Table::TYPE_TEXT, 5, [
        'nullable' => false,
        'default'  => ''
    ], 'Language')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable' => false,
        'default'  => ''
    ], 'Name')
    ->addIndex(
        $installer->getIdxName(
            'gshoppingv2/types',
            ['lang_idx', 'lang'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['lang_idx', 'lang'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE])
    ->setComment('Google Taxonomies');
$installer->getConnection()->createTable($table);

$installer->endSetup();

