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

namespace Bdcrops\GiftCard\Block\Adminhtml\Pool;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Bdcrops\GiftCard\Helper\Template;

/**
 * Class Edit
 * @package Bdcrops\GiftCard\Block\Adminhtml\Pool
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    public $_coreRegistry;

    /**
     * @var Template
     */
    protected $templateHelper;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Template $templateHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Template $templateHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->templateHelper = $templateHelper;

        parent::__construct($context, $data);
    }

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Bdcrops_GiftCard';
        $this->_controller = 'adminhtml_pool';

        parent::_construct();

        $this->buttonList->add(
            'saveandcontinue',
            [
                'label'          => __('Save and Continue Edit'),
                'class'          => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']]
                ]
            ],
            -100
        );

        $this->_formScripts[] = $this->templateHelper->getFormScript();
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $currentPool = $this->_coreRegistry->registry('current_pool');
        if ($currentPool && $currentPool->getId()) {
            return __("Edit Gift Code Pool '%1'", $this->escapeHtml($currentPool->getName()));
        }

        return __('New Gift Code Pool');
    }
}
