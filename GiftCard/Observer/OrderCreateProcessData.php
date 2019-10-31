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

namespace Bdcrops\GiftCard\Observer;

use Exception;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Bdcrops\GiftCard\Helper\Checkout;

/**
 * Class OrderCreateProcessData
 *
 * @package Bdcrops\GiftCard\Observer
 */
class OrderCreateProcessData implements ObserverInterface
{
    /**
     * @var Checkout
     */
    protected $_checkoutHelper;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * OrderCreateProcessData constructor.
     *
     * @param Checkout $checkoutHelper
     * @param ManagerInterface $messageManager
     * @param Escaper $escaper
     * @param RequestInterface $request
     */
    public function __construct(
        Checkout $checkoutHelper,
        ManagerInterface $messageManager,
        Escaper $escaper,
        RequestInterface $request
    ) {
        $this->_checkoutHelper = $checkoutHelper;
        $this->messageManager = $messageManager;
        $this->escaper = $escaper;
        $this->request = $request;
    }

    /**
     * Process post data and set usage of GC into order creation model
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $model = $observer->getEvent()->getOrderCreateModel();
        $data = $observer->getEvent()->getRequest();
        $quote = $model->getQuote();

        $isUsedCouponBox = $this->_checkoutHelper->isEnabled()
                           && $this->_checkoutHelper->isUsedCouponBox()
                           && isset($data['order']['coupon']['code']);

        if (isset($data['gc_apply_code'])) {
            $giftCode = trim((string) $data['gc_apply_code']);
        } else {
            if ($isUsedCouponBox) {
                $giftCode = trim($data['order']['coupon']['code']);

                unset($data['order']['coupon']['code']);
                $this->request->setPostValue('order', $data);
            }
        }

        if (!empty($giftCode)) {
            try {
                $this->_checkoutHelper->addGiftCards($giftCode);
                $this->messageManager->addSuccess(__('The gift code has been accepted.'));
            } catch (Exception $e) {
                $this->messageManager->addError(
                    __('The gift code "%1" is not valid.', $this->escaper->escapeHtml($giftCode))
                );
            }
        }

        if (isset($data['gc_cancel_code'])) {
            $cancelCode = trim((string) $data['gc_cancel_code']);
        } else {
            if (isset($giftCode) && empty($giftCode)) {
                $giftCards = $this->_checkoutHelper->getGiftCardsUsed();
                if (sizeof($giftCards)) {
                    $cancelCode = array_keys($giftCards)[0];
                }
            }
        }

        if (!empty($cancelCode)) {
            try {
                $this->_checkoutHelper->removeGiftCard($cancelCode);
                $this->messageManager->addSuccess(
                    __('You canceled the gift code "%1".', $this->escaper->escapeHtml($cancelCode))
                );
            } catch (Exception $e) {
                $this->messageManager->addError(__('Cancel gift code fail.'));
            }
        }

        if (isset($data['gc_apply_credit'])) {
            try {
                $this->_checkoutHelper->applyCredit($data['gc_apply_credit'], $quote->getCustomerId());

                $this->messageManager->addSuccess(__('Your amount was successfully credited.'));
            } catch (Exception $e) {
                $this->messageManager->addError(__('Credit amount for order fail.'));
            }
        }

        return $this;
    }
}
