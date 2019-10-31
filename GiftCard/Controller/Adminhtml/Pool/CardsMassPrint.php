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

namespace Bdcrops\GiftCard\Controller\Adminhtml\Pool;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Bdcrops\GiftCard\Controller\Adminhtml\Pool;
use Bdcrops\GiftCard\Helper\Template;
use Bdcrops\GiftCard\Model\PoolFactory;
use Spipu\Html2Pdf\Exception\Html2PdfException;

/**
 * Class CardsMassPrint
 * @package Bdcrops\GiftCard\Controller\Adminhtml\Pool
 */
class CardsMassPrint extends Pool
{
    /**
     * @var Template
     */
    protected $_template;

    /**
     * CardsMassPrint constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PoolFactory $poolFactory
     * @param Template $templateHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PoolFactory $poolFactory,
        Template $templateHelper
    ) {
        $this->_template = $templateHelper;

        parent::__construct($context, $resultPageFactory, $poolFactory);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Html2PdfException
     */
    public function execute()
    {
        $pool = $this->_initObject();

        if (!$pool->getId()) {
            $this->_forward('noroute');
        }

        $codesIds = $this->getRequest()->getParam('ids');

        if (is_array($codesIds)) {
            $collection = $this->_objectManager->create('Bdcrops\GiftCard\Model\ResourceModel\GiftCard\Collection')
                ->addFieldToFilter('giftcard_id', ['in' => $codesIds])
                ->addFieldToFilter('pool_id', $pool->getId());

            $output = $this->_template->outputGiftCardPdf($collection->getItems(), 'D');

            if (is_null($output)) {
                $this->messageManager->addErrorMessage(__('Gift cards can\'t print.'));
                $this->_redirect('*/*/');
            }
        }
    }
}
