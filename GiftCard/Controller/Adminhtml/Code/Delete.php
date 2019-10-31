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

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Bdcrops\GiftCard\Controller\Adminhtml\Code;

/**
 * Class Delete
 * @package Bdcrops\GiftCard\Controller\Adminhtml\Code
 */
class Delete extends Code
{
    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $giftCard = $this->_initObject();
        if ($giftCard && $giftCard->getId()) {
            try {
                $giftCard->delete();
                $this->messageManager->addSuccessMessage(__('The gift card was deleted successfully.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        $this->_redirect('*/*/');
    }
}
