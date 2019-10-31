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

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Payment\Model\Cart;
use Bdcrops\GiftCard\Helper\Checkout as GiftCardCheckoutHelper;

/**
 * Class PaypalPrepareItems
 * @package Bdcrops\GiftCard\Observer
 */
class PaypalPrepareItems implements ObserverInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * PaypalPrepareItems constructor.
     *
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Add reward amount to payment discount total
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Cart $cart */
        $cart = $observer->getEvent()->getCart();

        $quote = $this->checkoutSession->getQuote();

        /** Discount from Gift Card code*/
        $giftcard = GiftCardCheckoutHelper::jsonDecode($quote->getGiftCards());

        /** Discount from Gift Card Credit*/
        $gcCredit = $quote->getGcCredit();

        $discount = 0;
        if ($giftcard) {
            foreach ($giftcard as $k => $v) {
                $discount += $v;
            }
        }

        if ($discount > 0.0001) {
            $cart->addCustomItem('Gift Card', 1, -1.00 * $discount);
        }

        if ($gcCredit > 0.0001) {
            $cart->addCustomItem('Gift Card Credit', 1, -1.00 * $gcCredit);
        }
    }
}
