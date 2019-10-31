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
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Bdcrops\GiftCard\Model\GiftCard;
use Bdcrops\GiftCard\Model\GiftCardFactory;

/**
 * Class Code
 * @package Bdcrops\GiftCard\Controller\Adminhtml
 */
abstract class Code extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Bdcrops_GiftCard::code';

    /** @type PageFactory */
    protected $resultPageFactory;

    /** @var GiftCardFactory */
    protected $_giftCardFactory;

    /**
     * Code constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param GiftCardFactory $giftCardFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        GiftCardFactory $giftCardFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_giftCardFactory = $giftCardFactory;

        parent::__construct($context);
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return Page
     */
    protected function _initAction()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Bdcrops_GiftCard::code');
        $resultPage->addBreadcrumb(__('Gift Card'), __('Gift Card'));
        $resultPage->addBreadcrumb(__('Codes'), __('Codes'));

        return $resultPage;
    }

    /**
     * Init Gift Code
     *
     * @return bool|GiftCard
     */
    protected function _initObject()
    {
        $codeId = (int) $this->getRequest()->getParam('id');

        /** @var GiftCard $giftCard */
        $giftCard = $this->_giftCardFactory->create();
        if ($codeId) {
            $giftCard->load($codeId);
            if (!$giftCard->getId()) {
                $this->messageManager->addErrorMessage(__('This gift code no longer exists.'));

                return false;
            }
        }

        return $giftCard;
    }

    /**
     * Get gift code collection
     *
     * @return mixed
     */
    protected function _getCodeCollection()
    {
        return $this->_giftCardFactory->create()->getCollection();
    }
}
