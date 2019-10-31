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

namespace Bdcrops\GiftCard\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Bdcrops\GiftCard\Model\TemplateFactory;

/**
 * Class Template
 * @package Bdcrops\GiftCard\Controller\Adminhtml
 */
abstract class Template extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Bdcrops_GiftCard::template';

    /** @type PageFactory */
    protected $resultPageFactory;

    /** @var TemplateFactory */
    protected $_templateFactory;

    /**
     * Template constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TemplateFactory $templateFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TemplateFactory $templateFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_templateFactory = $templateFactory;

        parent::__construct($context);
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Bdcrops_GiftCard::template');
        $resultPage->addBreadcrumb(__('Gift Card'), __('Gift Card'));
        $resultPage->addBreadcrumb(__('Templates'), __('Templates'));

        return $resultPage;
    }

    /**
     * Init Gift Code
     *
     * @return bool|\Bdcrops\GiftCard\Model\Template
     */
    protected function _initObject()
    {
        $id = (int) $this->getRequest()->getParam('id');

        /** @var \Bdcrops\GiftCard\Model\Template $template */
        $template = $this->_templateFactory->create();
        if ($id) {
            $template->load($id);
            if (!$template->getId()) {
                $this->messageManager->addErrorMessage(__('This template no longer exists.'));

                return false;
            }
        }

        return $template;
    }

    /**
     * Get gift code collection
     *
     * @return mixed
     */
    protected function _getTemplateCollection()
    {
        return $this->_templateFactory->create()->getCollection();
    }
}
