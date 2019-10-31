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

namespace Bdcrops\GiftCard\Controller\Adminhtml\Code;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Bdcrops\GiftCard\Controller\Adminhtml\Code;
use Bdcrops\GiftCard\Model\GiftCardFactory;

/**
 * Class History
 * @package Bdcrops\GiftCard\Controller\Adminhtml\Code
 */
class History extends Code
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * History constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param GiftCardFactory $giftCardFactory
     * @param Registry $registry
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        GiftCardFactory $giftCardFactory,
        Registry $registry,
        LayoutFactory $resultLayoutFactory
    ) {
        $this->registry = $registry;
        $this->resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($context, $resultPageFactory, $giftCardFactory);
    }

    /**
     * @return Layout
     */
    public function execute()
    {
        $giftCard = $this->_initObject();
        $this->registry->register('current_giftcard', $giftCard);

        $resultLayout = $this->resultLayoutFactory->create();

        return $resultLayout;
    }
}
