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

namespace Bdcrops\GiftCard\Plugin\Block;

use Magento\Checkout\Block\Cart\Coupon;
use Magento\Framework\Exception\LocalizedException;
use Bdcrops\GiftCard\Helper\Checkout as CheckoutHelper;

/**
 * Class CartCoupon
 * @package Bdcrops\GiftCard\Plugin
 */
class CartCoupon
{
    /**
     * @type CheckoutHelper
     */
    protected $helper;

    /**
     * CartCoupon contructor.
     *
     * @param CheckoutHelper $checkoutHelper
     */
    public function __construct(CheckoutHelper $checkoutHelper)
    {
        $this->helper = $checkoutHelper;
    }

    /**
     * @param Coupon $subject
     * @param                                     $coupon
     *
     * @return mixed
     */
    public function afterGetCouponCode(Coupon $subject, $coupon)
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

    /**
     * @param Coupon $subject
     * @param string $html
     *
     * @return string
     * @throws LocalizedException
     */
    public function afterToHtml(Coupon $subject, $html)
    {
        $giftCardHtml = $subject->getLayout()
            ->createBlock(
                'Magento\Framework\View\Element\Template',
                'bdcrops.gift.card.checkout.cart.coupon'
            )
            ->setTemplate('Bdcrops_GiftCard::cart/coupon.phtml');

        return $giftCardHtml->toHtml() . $html;
    }
}
