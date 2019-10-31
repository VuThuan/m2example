<?php
/**
 * Bdcrops
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Bdcrops.com license sliderConfig is
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

namespace Bdcrops\GiftCard\Model\Api;

use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Bdcrops\GiftCard\Api\GiftCardManagementInterface;
use Bdcrops\GiftCard\Api\GuestGiftCardManagementInterface;

/**
 * Coupon management class for guest carts.
 */
class GuestGiftCardManagement implements GuestGiftCardManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var GiftCardManagementInterface
     */
    private $couponManagement;

    /**
     * Constructs a coupon read service object.
     *
     * @param GiftCardManagementInterface $couponManagement
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        GiftCardManagementInterface $couponManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->couponManagement = $couponManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $code)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->couponManagement->set($quoteIdMask->getQuoteId(), $code);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId, $code)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->couponManagement->remove($quoteIdMask->getQuoteId(), $code);
    }
}
