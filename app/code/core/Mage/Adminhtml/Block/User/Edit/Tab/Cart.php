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
 * Adminhtml user orders grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Camilooframework Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_User_Edit_Tab_Cart extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setId('user_cart_grid'.$this->getWebsiteId());
        $this->setUseAjax(true);
        $this->_parentTemplate = $this->getTemplate();
        $this->setTemplate('user/tab/cart.phtml');
    }

    protected function _prepareCollection()
    {
        $user = Mage::registry('current_user');
        $storeIds = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();

        $quote = Mage::getModel('sales/quote')
            ->setSharedStoreIds($storeIds)
            ->loadByUser($user);

        if ($quote) {
            $collection = $quote->getItemsCollection(false);
        }
        else {
            $collection = new Varien_Data_Collection();
        }

        $collection->addFieldToFilter('parent_item_id', array('null' => true));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header'    => Mage::helper('adminhtml')->__('Product ID'),
            'index'     => 'product_id',
            'width'     => '100px',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('adminhtml')->__('Product Name'),
            'index'     => 'name',
            'renderer'  => 'adminhtml/user_edit_tab_view_grid_renderer_item'
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('adminhtml')->__('SKU'),
            'index'     => 'sku',
            'width'     => '100px',
        ));

        $this->addColumn('qty', array(
            'header'    => Mage::helper('adminhtml')->__('Qty'),
            'index'     => 'qty',
            'type'      => 'number',
            'width'     => '60px',
        ));

        $this->addColumn('price', array(
            'header'        => Mage::helper('adminhtml')->__('Price'),
            'index'         => 'price',
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('total', array(
            'header'        => Mage::helper('adminhtml')->__('Total'),
            'index'         => 'row_total',
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('user')->__('Action'),
            'index'     => 'quote_item_id',
            'renderer'  => 'adminhtml/user_grid_renderer_multiaction',
            'filter'    => false,
            'sortable'  => false,
            'actions'   => array(
                array(
                    'caption'           => Mage::helper('user')->__('Configure'),
                    'url'               => 'javascript:void(0)',
                    'process'           => 'configurable',
                    'control_object'    => $this->getJsObjectName() . 'cartControl'
                ),
                array(
                    'caption'   => Mage::helper('user')->__('Delete'),
                    'url'       => '#',
                    'onclick'   => 'return ' . $this->getJsObjectName() . 'cartControl.removeItem($item_id);'
                )
            )
        ));

        return parent::_prepareColumns();
    }

    /**
     * Gets user assigned to this block
     *
     * @return Mage_User_Model_User
     */
    public function getUser() {
        return Mage::registry('current_user');
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/cart', array('_current'=>true, 'website_id' => $this->getWebsiteId()));
    }

    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', array('id' => $row->getProductId()));
    }
}
