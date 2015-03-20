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
class Zookal_GShoppingV2_Model_Resource_Taxonomy extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('gshoppingv2/taxonomies', 'id');
    }
}
