<?php
/** @var $installer Zookal_GShoppingV2_Model_Resource_Setup */
$installer = $this;

/** @var Magento_Db_Adapter_Pdo_Mysql $connection */
$connection = $installer->getConnection();

foreach ($installer->getTaxonomies() as $lang => $taxonomy) {
    $data = [];
    foreach ($taxonomy as $i => $t) {
        $data[] = [$i, $lang, $t];
    }
    $connection->insertArray(
        $this->getTable('gshoppingv2/taxonomies'),
        ['lang_idx', 'lang', 'name'],
        $data
    );
}
