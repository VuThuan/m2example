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

namespace Bdcrops\GiftCard\Controller\Adminhtml\Template;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Bdcrops\GiftCard\Controller\Adminhtml\Template;
use Bdcrops\GiftCard\Helper\Data;
use Bdcrops\GiftCard\Model\TemplateFactory;

/**
 * Class Edit
 * @package Bdcrops\GiftCard\Controller\Adminhtml\Template
 */
class Edit extends Template
{
    /** @var Registry */
    protected $registry;

    /** @var Data */
    protected $_giftcardHelper;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TemplateFactory $templateFactory
     * @param Registry $registry
     * @param Data $giftcardHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TemplateFactory $templateFactory,
        Registry $registry,
        Data $giftcardHelper
    ) {
        $this->registry = $registry;
        $this->_giftcardHelper = $giftcardHelper;

        parent::__construct($context, $resultPageFactory, $templateFactory);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $template = $this->_initObject();
        if ($template) {
            $templateCollection = $this->_getTemplateCollection();
            $template->setTemplateCollection($templateCollection->getData());

            //Set entered data if was error when we do save
            $data = $this->_session->getTemplateFormData(true);
            if (!empty($data)) {
                $template->addData($data);
            }

            $this->registry->register('current_template', $template);

            /** @var Page $resultPage */
            $resultPage = $this->_initAction();
            $resultPage->getConfig()->getTitle()->prepend($template->getId() ? __(
                'Edit Template "%1"',
                $template->getName()
            ) : __('Create New Template'));

            return $resultPage;
        }
    }
}
