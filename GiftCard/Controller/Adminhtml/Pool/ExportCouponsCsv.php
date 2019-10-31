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

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Bdcrops\GiftCard\Controller\Adminhtml\Pool;
use Bdcrops\GiftCard\Model\PoolFactory;

/**
 * Class ExportCouponsCsv
 * @package Bdcrops\GiftCard\Controller\Adminhtml\Template
 */
class ExportCouponsCsv extends Pool
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * Constructor.
     *
     * @param Registry $registry
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PoolFactory $poolFactory
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Registry $registry,
        Context $context,
        PageFactory $resultPageFactory,
        PoolFactory $poolFactory,
        FileFactory $fileFactory
    ) {
        $this->_coreRegistry = $registry;
        $this->_fileFactory = $fileFactory;

        parent::__construct($context, $resultPageFactory, $poolFactory);
    }

    /**
     * Export coupon codes as CSV file
     *
     * @return ResponseInterface|ResultInterface|null
     * @throws Exception
     */
    public function execute()
    {
        $pool = $this->_initObject();

        if ($pool->getId()) {
            $fileName = "pool_{$pool->getId()}.csv";
            $this->_coreRegistry->register('current_pool', $pool);

            $content = $this->_view->getLayout()
                ->createBlock('Bdcrops\GiftCard\Block\Adminhtml\Pool\Edit\Tab\Generate\Grid')
                ->getCsvFile();

            return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
        }

        return null;
    }
}
