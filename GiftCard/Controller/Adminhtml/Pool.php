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
use Bdcrops\GiftCard\Model\GiftCardFactory;
use Bdcrops\GiftCard\Model\PoolFactory;

/**
 * Class Pool
 * @package Bdcrops\GiftCard\Controller\Adminhtml
 */
abstract class Pool extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Bdcrops_GiftCard::pool';

    /** @type PageFactory */
    protected $resultPageFactory;

    /** @var GiftCardFactory */
    protected $_poolFactory;

    /**
     * Pool constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PoolFactory $poolFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PoolFactory $poolFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_poolFactory = $poolFactory;

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
        $resultPage->setActiveMenu('Bdcrops_GiftCard::pool');
        $resultPage->addBreadcrumb(__('Gift Card'), __('Gift Card'));
        $resultPage->addBreadcrumb(__('Pools'), __('Pools'));

        return $resultPage;
    }

    /**
     * Init Gift Code
     *
     * @return bool|\Bdcrops\GiftCard\Model\Pool
     */
    protected function _initObject()
    {
        $poolId = (int) $this->getRequest()->getParam('id');
        if (!$poolId && $this->getRequest()->getParam('pool_id')) {
            $poolId = (int) $this->getRequest()->getParam('pool_id');
        }

        /** @var \Bdcrops\GiftCard\Model\Pool $pool */
        $pool = $this->_poolFactory->create();
        if ($poolId) {
            $pool->load($poolId);
            if (!$pool->getId()) {
                $this->messageManager->addErrorMessage(__('This gift card pool no longer exists.'));

                return false;
            }
        }

        return $pool;
    }

    /**
     * Get gift code pool collection
     *
     * @return mixed
     */
    protected function _getPoolCollection()
    {
        return $this->_poolFactory->create()->getCollection();
    }
}
