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
 * @method int getId()
 * @method Zookal_GShoppingV2_Model_Taxonomy setId(int $value)
 * @method int getLangIdx()
 * @method Zookal_GShoppingV2_Model_Taxonomy setLangIdx(int $value)
 * @method string getLang()
 * @method Zookal_GShoppingV2_Model_Taxonomy setLang(string $value)
 * @method string getName()
 * @method Zookal_GShoppingV2_Model_Taxonomy setName(string $value)
 */
class Zookal_GShoppingV2_Model_Taxonomy extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('gshoppingv2/taxonomy');
    }
}
