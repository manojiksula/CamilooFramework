<?php
/**
 * Camilooframework
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Camilooframework to newer
 * versions in the future. If you wish to customize Camilooframework for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * User account form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Camilooframework Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_User_Edit_Tab_Newsletter extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('user/tab/newsletter.phtml');
    }

    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_newsletter');
        $user = Mage::registry('current_user');
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByUser($user);
        Mage::register('subscriber', $subscriber);

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('user')->__('Newsletter Information')));

        $fieldset->addField('subscription', 'checkbox',
             array(
                    'label' => Mage::helper('user')->__('Subscribed to Newsletter?'),
                    'name'  => 'subscription'
             )
        );

        if ($user->isReadonly()) {
            $form->getElement('subscription')->setReadonly(true, true);
        }

        $form->getElement('subscription')->setIsChecked($subscriber->isSubscribed());

        if($changedDate = $this->getStatusChangedDate()) {
             $fieldset->addField('change_status_date', 'label',
                 array(
                        'label' => $subscriber->isSubscribed() ? Mage::helper('user')->__('Last Date Subscribed') : Mage::helper('user')->__('Last Date Unsubscribed'),
                        'value' => $changedDate,
                        'bold'  => true
                 )
            );
        }


        $this->setForm($form);
        return $this;
    }

    public function getStatusChangedDate()
    {
        $subscriber = Mage::registry('subscriber');
        if($subscriber->getChangeStatusAt()) {
            return $this->formatDate($subscriber->getChangeStatusAt(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
        }

        return null;
    }

    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()->createBlock('adminhtml/user_edit_tab_newsletter_grid','newsletter.grid')
        );
        return parent::_prepareLayout();
    }

}