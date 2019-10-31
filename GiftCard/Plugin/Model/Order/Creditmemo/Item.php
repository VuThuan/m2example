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

namespace Bdcrops\GiftCard\Plugin\Model\Order\Creditmemo;

use Closure;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;

/**
 * Class Item
 * @package Bdcrops\GiftCard\Plugin\Model\Order\Creditmemo
 */
class Item
{
    /**
     * @param CreditmemoItem $item
     * @param Closure $proceed
     *
     * @return CreditmemoItem|mixed
     * @throws LocalizedException
     */
    public function aroundCalcRowTotal(CreditmemoItem $item, Closure $proceed)
    {
        $orderItem = $item->getOrderItem();
        $giftCards = $orderItem->getProductOptionByCode('giftcards');
        if ($giftCards) {
            $giftCardQty = count($giftCards);
        }

        $refundableGiftCard = $orderItem->getProductOptionByCode('refundable_gift_card');
        if ($refundableGiftCard) {
            $refundableGiftCardQty = count($refundableGiftCard);
        }

        if ($orderItem->getProductType() !== 'mpgiftcard' || !$giftCardQty || ($giftCardQty == $refundableGiftCardQty)) {
            return $proceed();
        }

        $rate = $refundableGiftCardQty / $giftCardQty;
        $creditmemo = $item->getCreditmemo();
        $orderItemQtyInvoiced = $orderItem->getQtyInvoiced();

        $rowTotal = ($orderItem->getRowInvoiced() - $orderItem->getAmountRefunded()) * $rate;
        $baseRowTotal = ($orderItem->getBaseRowInvoiced() - $orderItem->getBaseAmountRefunded()) * $rate;
        $rowTotalInclTax = $orderItem->getRowTotalInclTax() * $rate;
        $baseRowTotalInclTax = $orderItem->getBaseRowTotalInclTax() * $rate;

        $qty = $this->processQty($item);
        if (!$item->isLast() && $orderItemQtyInvoiced > 0 && $qty >= 0) {
            $availableQty = $orderItemQtyInvoiced - $orderItem->getQtyRefunded();
            $rowTotal = $creditmemo->roundPrice($rowTotal / $availableQty * $qty);
            $baseRowTotal = $creditmemo->roundPrice($baseRowTotal / $availableQty * $qty, 'base');
        }
        $item->setRowTotal($rowTotal);
        $item->setBaseRowTotal($baseRowTotal);

        if ($rowTotalInclTax && $baseRowTotalInclTax) {
            $orderItemQty = $orderItem->getQtyOrdered();
            $item->setRowTotalInclTax(
                $creditmemo->roundPrice($rowTotalInclTax / $orderItemQty * $qty, 'including')
            );
            $item->setBaseRowTotalInclTax(
                $creditmemo->roundPrice($baseRowTotalInclTax / $orderItemQty * $qty, 'including_base')
            );
        }

        return $item;
    }

    /**
     * @return int|float
     * @throws LocalizedException
     */
    private function processQty($item)
    {
        $orderItem = $item->getOrderItem();
        $qty = $item->getQty();
        if ($orderItem->getIsQtyDecimal()) {
            $qty = (double) $qty;
        } else {
            $qty = (int) $qty;
        }
        $qty = max(0, $qty);
        if ($this->isQtyAvailable($qty, $orderItem)) {
            return $qty;
        } else {
            throw new LocalizedException(
                __('We found an invalid quantity to refund item "%1".', $this->getName())
            );
        }
    }

    /**
     * Checks if quantity available for refund
     *
     * @param int $qty
     * @param \Magento\Sales\Model\Order\Item $orderItem
     *
     * @return bool
     */
    private function isQtyAvailable($qty, \Magento\Sales\Model\Order\Item $orderItem)
    {
        return $qty <= $orderItem->getQtyToRefund() || $orderItem->isDummy();
    }
}
