<?php
/**
 * Bdcrops
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Bdcrops.com license that is
 * available through the world-wide-web at this URL:
 * https://www.bdcrops.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Bdcrops
 * @package     Bdcrops_GiftCard
 * @copyright   Copyright (c) Bdcrops (https://www.bdcrops.com/)
 * @license     https://www.bdcrops.com/LICENSE.txt
 */

namespace Bdcrops\GiftCard\Block\Adminhtml\Pool\Edit\Tab\Generate;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\SalesRule\Helper\Coupon;
use Bdcrops\GiftCard\Model\Pool;

/**
 * Class Information
 * @package Bdcrops\GiftCard\Block\Adminhtml\Pool\Edit\Tab
 */
class Form extends Generic
{
    /**
     * Sales rule coupon
     *
     * @var Coupon
     */
    protected $_salesRuleCoupon = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Coupon $salesRuleCoupon
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Coupon $salesRuleCoupon,
        array $data = []
    ) {
        $this->_salesRuleCoupon = $salesRuleCoupon;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare coupon codes generation parameters form
     *
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /* @var $model Pool */
        $model = $this->_coreRegistry->registry('current_pool');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('pool_');

        $fieldset = $form->addFieldset('generate_fieldset', ['legend' => __('Pool Information')]);
        $fieldset->addClass('ignore-validate');

        $gridBlock = $this->getLayout()->getBlock('bdcrops_giftcard_pool_edit_tab_generate_grid');
        $gridBlockJsObject = '';
        if ($gridBlock) {
            $gridBlockJsObject = $gridBlock->getJsObjectName();
        }

        $fieldset->addField('pattern', 'text', [
            'name'     => 'pattern',
            'label'    => __('Code Pattern'),
            'title'    => __('Code Pattern'),
            'required' => true
        ]);

        $fieldset->addField('qty', 'text', [
            'name'     => 'qty',
            'label'    => __('Gift Card Qty'),
            'title'    => __('Gift Card Qty'),
            'required' => true,
            'class'    => 'validate-digits validate-greater-than-zero'
        ]);

        $fieldset->addField(
            'generate_button',
            'note',
            [
                'text' => $this->getButtonHtml(
                    __('Generate'),
                    "generatePoolCodes('{$form->getHtmlIdPrefix()}' ,'{$this->getGenerateUrl()}', '{$gridBlockJsObject}')",
                    'generate'
                )
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve URL to Generate Action
     *
     * @return string
     */
    public function getGenerateUrl()
    {
        return $this->getUrl('*/*/generate');
    }
}
