<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Content Type resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Model_Resource_Type extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('gshoppingv2/types', 'type_id');
    }

    /**
     * Return Type ID by Attribute Set Id and target country
     *
     * @param Zookal_GShoppingV2_Model_Type $model
     * @param int                                        $attributeSetId Attribute Set
     * @param string                                     $targetCountry  Two-letters country ISO code
     *
     * @return Zookal_GShoppingV2_Model_Type
     */
    public function loadByAttributeSetIdAndTargetCountry($model, $attributeSetId, $targetCountry)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('attribute_set_id=?', $attributeSetId)
            ->where('target_country=?', $targetCountry);

        $data = $this->_getReadAdapter()->fetchRow($select);
        $data = is_array($data) ? $data : [];
        $model->setData($data);
        return $model;
    }
}
