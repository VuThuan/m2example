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

namespace Bdcrops\GiftCard\Plugin\Block\Order\Create;

use Bdcrops\GiftCard\Helper\Checkout as CheckoutHelper;

/**
 * Class Coupons
 * @package Bdcrops\GiftCard\Plugin\Block\Order\Create
 */
class Coupons
{
    /**
     * @type CheckoutHelper
     */
    protected $helper;

    /**
     * Coupons contructor.
     *
     * @param CheckoutHelper $checkoutHelper
     */
    public function __construct(CheckoutHelper $checkoutHelper)
    {
        $this->helper = $checkoutHelper;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Coupons $subject
     * @param                                                     $coupon
     *
     * @return mixed
     */
    public function afterGetCouponCode(\Magento\Sales\Block\Adminhtml\Order\Create\Coupons $subject, $coupon)
    {
        if (!$this->helper->isEnabled() || !$this->helper->isUsedCouponBox()) {
            return $coupon;
        }

        $giftCards = $this->helper->getGiftCardsUsed();
        if (sizeof($giftCards)) {
            return array_keys($giftCards)[0];
        }

        return $coupon;
    }
}
