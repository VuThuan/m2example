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

namespace Bdcrops\GiftCard\Model\Total\Quote;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote\Item;
use Bdcrops\GiftCard\Helper\Checkout as GiftCardCheckoutHelper;
use Bdcrops\GiftCard\Model\GiftCardFactory;
use Bdcrops\GiftCard\Model\Product\Type\GiftCard;

/**
 * Class Discount
 *
 * @package Bdcrops\GiftCard\Model\Total\Quote
 */
class Discount extends AbstractTotal
{
    /**
     * @var GiftCardCheckoutHelper
     */
    protected $_helper;

    /**
     * @var GiftCardFactory
     */
    protected $_giftCardFactory;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var string
     */
    private $_creditCode = 'gift_credit';

    /**
     * Discount constructor.
     *
     * @param GiftCardCheckoutHelper $helper
     * @param GiftCardFactory $giftCardFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        GiftCardCheckoutHelper $helper,
        GiftCardFactory $giftCardFactory,
        ManagerInterface $messageManager
    ) {
        $this->_helper = $helper;
        $this->_giftCardFactory = $giftCardFactory;
        $this->_messageManager = $messageManager;

        $this->setCode('gift_card');
    }

    /**
     * Collect grand total address amount
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     *
     * @return $this|AbstractTotal
     * @throws LocalizedException
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        if (!$this->_helper->isEnabled()
            || ($quote->isVirtual() && $this->_getAddress()->getAddressType() == Address::ADDRESS_TYPE_SHIPPING)
            || (!$quote->isVirtual() && $this->_getAddress()->getAddressType() == Address::ADDRESS_TYPE_BILLING)
        ) {
            return $this;
        }

        $this->calculateGiftCardDiscount($quote, $total);
        $this->calculateGiftCreditDiscount($quote, $total);

        return $this;
    }

    /**
     * Discount by gift card codes
     *
     * @param Quote $quote
     * @param Total $total
     *
     * @return $this
     */
    protected function calculateGiftCardDiscount(Quote $quote, Total $total)
    {
        $giftCards = $this->_helper->getGiftCardsUsed();
        if (!$giftCards || !sizeof($giftCards)) {
            $quote->setGiftCards(null);
            $this->_helper->saveGiftCardsData(
                [
                    'gift_cards'            => [],
                    'base_gift_card_amount' => 0,
                    'gift_card_amount'      => 0
                ]
            );

            return $this;
        }

        /**
         * Reset Gift Card Amount Used
         * If multiple is not allow, only apply the last gift code
         */
        $multipleAllow = $this->_helper->isUsedMultipleCode($quote->getStore());
        foreach ($giftCards as $code => $amount) {
            if ((sizeof($giftCards) > 1) && !$multipleAllow) {
                unset($giftCards[$code]);
            } else {
                $giftCards[$code] = 0;
            }
        }

        $totalAmount = $this->getTotalAmountForDiscount($quote, $total);
        $baseTotalDiscount = 0;
        foreach ($giftCards as $code => $value) {
            $giftCard = $this->_giftCardFactory->create()->loadByCode($code);
            if (!$giftCard->isActive()) {
                $this->_messageManager->addErrorMessage(__('The gift card code "%1" is not valid.', $code));
                unset($giftCards[$code]);
                continue;
            }
            if ($totalAmount > $baseTotalDiscount) {
                $amount = min($giftCard->getBalance(), $totalAmount - $baseTotalDiscount);
                $baseTotalDiscount += $amount;

                $giftCards[$code] = $amount;
            }
        }

        if ($baseTotalDiscount == $totalAmount) {
            $totalDiscount = $totalAmount;
        } else {
            $totalDiscount = $this->_helper->convertPrice($baseTotalDiscount, false, false, $quote->getStore());
        }

        if ($totalDiscount > 0.0001) {
            $total->setTotalAmount($this->getCode(), -$totalDiscount);
            $total->setBaseTotalAmount($this->getCode(), -$baseTotalDiscount);

            $total->setGrandTotal($total->getGrandTotal() - $totalDiscount);
            $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseTotalDiscount);
        }

        $this->_helper->saveGiftCardsData(
            [
                'gift_cards'            => $giftCards,
                'base_gift_card_amount' => $baseTotalDiscount,
                'gift_card_amount'      => $totalDiscount,
            ]
        );
        $quote->setGiftCards(GiftCardCheckoutHelper::jsonEncode($giftCards));

        return $this;
    }

    /**
     * Discount by gift credit
     *
     * @param Quote $quote
     * @param Total $total
     *
     * @return $this
     * @throws NoSuchEntityException
     */
    protected function calculateGiftCreditDiscount(Quote $quote, Total $total)
    {
        $total->setTotalAmount($this->_creditCode, 0);
        $total->setBaseTotalAmount($this->_creditCode, 0);

        $creditAmount = $this->_helper->getGiftCreditUsed();
        if ($creditAmount <= 0) {
            $this->_helper->saveGiftCardsData(
                [
                    'gift_credit'             => 0,
                    'base_gift_credit_amount' => 0,
                    'gift_credit_amount'      => 0
                ]
            );

            return $this;
        }

        $customerBalance = $this->_helper->getCustomerBalance($quote->getCustomer()->getId());
        $baseTotalAmount = $this->getTotalAmountForDiscount($quote, $total);
        $totalAmount = $this->_helper->convertPrice($baseTotalAmount, false);

        $creditAmount = min($creditAmount, $totalAmount, $customerBalance);
        $baseCreditAmount = $creditAmount / $this->_helper->convertPrice(1, false);

        $total->setTotalAmount($this->_creditCode, -$creditAmount);
        $total->setBaseTotalAmount($this->_creditCode, -$baseCreditAmount);

        $total->setGrandTotal($total->getGrandTotal() - $creditAmount);
        $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseCreditAmount);

        $this->_helper->saveGiftCardsData(
            [
                'gift_credit'             => $creditAmount,
                'base_gift_credit_amount' => $baseCreditAmount,
                'gift_credit_amount'      => $creditAmount
            ]
        );

        $quote->setGcCredit($creditAmount);

        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param Quote $quote
     * @param Total $total
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(Quote $quote, Total $total)
    {
        if (!$this->_helper->isEnabled()) {
            return null;
        }

        $totalArray = [];
        $giftCards = $this->_helper->getGiftCardsData();
        if (isset($giftCards['gift_card_amount']) && $giftCards['gift_card_amount'] > 0.0001) {
            $totalArray[] = [
                'code'  => $this->getCode(),
                'title' => __('Gift Cards'),
                'value' => -$giftCards['gift_card_amount']
            ];
        }
        if (isset($giftCards['gift_credit_amount']) && $giftCards['gift_credit_amount'] > 0.0001) {
            $totalArray[] = [
                'code'  => $this->_creditCode,
                'title' => __('Credit'),
                'value' => -$giftCards['gift_credit_amount']
            ];
        }

        return $totalArray;
    }

    /**
     * Calculate total amount for discount
     *
     * @param Quote $quote
     * @param Total $total
     *
     * @return mixed
     */
    public function getTotalAmountForDiscount(Quote $quote, Total $total)
    {
        $discountTotal = $total->getBaseGrandTotal();
        if (!$this->_helper->canUsedForShipping($quote->getStoreId())) {
            $discountTotal -= $total->getBaseShippingAmount();
        }

        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            // todo use configuration to select which type of product can be spent by gift card
            if ($item->getProductType() == GiftCard::TYPE_GIFTCARD) {
                $discountTotal -= $item->getBaseRowTotal();
            }
        }

        return $discountTotal;
    }
}
