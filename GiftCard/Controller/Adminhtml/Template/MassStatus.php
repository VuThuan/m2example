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

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Bdcrops\GiftCard\Controller\Adminhtml\Template;
use Bdcrops\GiftCard\Model\TemplateFactory;

/**
 * Class MassStatus
 * @package Bdcrops\GiftCard\Controller\Adminhtml\Template
 */
class MassStatus extends Template
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * MassStatus constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TemplateFactory $templateFactory
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TemplateFactory $templateFactory,
        Filter $filter
    ) {
        $this->filter = $filter;

        parent::__construct($context, $resultPageFactory, $templateFactory);
    }

    /**
     * Update product(s) status action
     *
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->_getTemplateCollection());
        $status = (int) $this->getRequest()->getParam('status');

        $updated = 0;
        foreach ($collection as $template) {
            try {
                $template->setStatus($status)
                    ->save();
                $updated++;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    __('Something went wrong while updating status for %1.', $template->getCode())
                );
            }
        }

        if ($updated) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $updated));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
