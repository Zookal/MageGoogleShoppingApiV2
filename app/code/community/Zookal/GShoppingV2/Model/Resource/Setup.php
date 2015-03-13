<?php

/**
 * Class Zookal_GShoppingV2_Model_Resource_Setup
 */
class Zookal_GShoppingV2_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
{
    /**
     * @return array
     */
    public function getTaxonomies()
    {
        $taxonomyPath = [
            Mage::getModuleDir('', 'Zookal_GShoppingV2'),
            'data',
            'gshoppingv2_setup',
            'taxonomies',
        ];
        $taxonomyPath = implode(DS, $taxonomyPath) . DS;
        $files        = glob($taxonomyPath . '*.txt');
        $_options     = [];
        foreach ($files as $file) {

            $lang = str_replace('.txt', '', basename($file));
            if (!isset($_options[$lang])) {
                $_options[$lang] = [];
            }
            $_options[$lang] = file($file);
            unset($_options[$lang][0]); // unset first line which is a comment
            array_map('trim', $_options[$lang]);
        }
        return $_options;
    }
}
