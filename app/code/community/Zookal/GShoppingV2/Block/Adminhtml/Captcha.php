<?php
/**
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Google Content Captcha challenge
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zookal_GShoppingV2_Block_Adminhtml_Captcha extends Mage_Adminhtml_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('gshoppingv2/captcha.phtml');
    }

    /**
     * Get HTML code for confirm captcha button
     *
     * @return string
     */
    public function getConfirmButtonHtml()
    {
        $confirmButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData([
                'label'   => $this->__('Confirm'),
                'onclick' => "if($('user_confirm').value != '') { setLocation('" .
                    $this->getUrl(
                        '*/*/confirmCaptcha', ['_current' => true]
                    ) .
                    "' + 'user_confirm/' + $('user_confirm').value + '/'); }",
                'class'   => 'task'
            ]);
        return $confirmButton->toHtml();
    }
}
