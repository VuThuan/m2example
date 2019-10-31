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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Bdcrops\GiftCard\Controller\Adminhtml\Template;

/**
 * Class Delete
 * @package Bdcrops\GiftCard\Controller\Adminhtml\Template
 */
class Delete extends Template
{
    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $template = $this->_initObject();
        if ($template && $template->getId()) {
            try {
                $template->delete();
                $this->messageManager->addSuccessMessage(__('The template was deleted successfully.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        $this->_redirect('*/*/');
    }
}
