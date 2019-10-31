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

namespace Bdcrops\GiftCard\Model\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Bdcrops\GiftCard\Helper\Data;

/**
 * Class Discount
 *
 * @package Bdcrops\GiftCard\Model\Total\Creditmemo
 */
class Discount extends AbstractTotal
{
    /**
     * Collect Creditmemo subtotal
     *
     * @param Creditmemo $creditmemo
     *
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $baseOrderDiscount = $order->getBaseGiftCardAmount();
        $baseCreditDiscount = $order->getBaseGiftCreditAmount();
        $isRefundGC = false;
        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() == 'mpgiftcard'
                && $item->getProductOptionByCode('refundable_gift_card')
                && count($item->getProductOptionByCode('refundable_gift_card'))
            ) {
                $isRefundGC = true;
                break;
            }
        }
        if (!$baseOrderDiscount && !$baseCreditDiscount && !$isRefundGC) {
            return $this;
        }

        $rate = $creditmemo->getSubtotal() / $order->getSubtotal();

        if ($baseOrderDiscount) {
            $orderDiscount = $order->getGiftCardAmount();

            $giftcardDiscount = $creditmemo->roundPrice($orderDiscount * $rate, 'regular', true);
            $baseGiftcardDiscount = $creditmemo->roundPrice($baseOrderDiscount * $rate, 'base', true);

            $baseInvoiceDiscount = 0;
            $invoiceDiscount = 0;
            foreach ($creditmemo->getOrder()->getInvoiceCollection() as $previousInvoice) {
                $baseInvoiceDiscount += $previousInvoice->getBaseGiftCardAmount();
                $invoiceDiscount += $previousInvoice->getGiftCardAmount();
            }
            foreach ($creditmemo->getOrder()->getCreditmemosCollection() as $previousCreditmemo) {
                $baseInvoiceDiscount -= $previousCreditmemo->getBaseGiftCardAmount();
                $invoiceDiscount -= $previousCreditmemo->getGiftCardAmount();
            }

            $giftcardDiscount = max($invoiceDiscount, $giftcardDiscount);
            $baseGiftcardDiscount = max($baseInvoiceDiscount, $baseGiftcardDiscount);

            $creditmemo->setGiftCardAmount($giftcardDiscount);
            $creditmemo->setBaseGiftCardAmount($baseGiftcardDiscount);

            $this->getRefundGiftCards($creditmemo);

            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $giftcardDiscount);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseGiftcardDiscount);
        }

        if ($baseCreditDiscount) {
            $orderDiscount = $order->getGiftCreditAmount();

            $giftcardDiscount = $creditmemo->roundPrice($orderDiscount * $rate, 'regular', true);
            $baseGiftcardDiscount = $creditmemo->roundPrice($baseCreditDiscount * $rate, 'base', true);

            $baseInvoiceDiscount = 0;
            $invoiceDiscount = 0;
            foreach ($creditmemo->getOrder()->getInvoiceCollection() as $previousInvoice) {
                $baseInvoiceDiscount += $previousInvoice->getBaseGiftCreditAmount();
                $invoiceDiscount += $previousInvoice->getGiftCreditAmount();
            }
            foreach ($creditmemo->getOrder()->getCreditmemosCollection() as $previousCreditmemo) {
                $baseInvoiceDiscount -= $previousCreditmemo->getBaseGiftCreditAmount();
                $invoiceDiscount -= $previousCreditmemo->getGiftCreditAmount();
            }

            $giftcardDiscount = max($invoiceDiscount, $giftcardDiscount);
            $baseGiftcardDiscount = max($baseInvoiceDiscount, $baseGiftcardDiscount);

            $creditmemo->setGiftCreditAmount($giftcardDiscount);
            $creditmemo->setBaseGiftCreditAmount($baseGiftcardDiscount);

            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $giftcardDiscount);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseGiftcardDiscount);
        }

        $creditmemo->setRefundGiftCardFlag(true);

        return $this;
    }

    /**
     * @param $creditmemo
     *
     * @return $this
     */
    protected function getRefundGiftCards($creditmemo)
    {
        $order = $creditmemo->getOrder();

        $rate = $creditmemo->getBaseGiftCardAmount() / $order->getBaseGiftCardAmount();
        $giftCards = Data::jsonDecode($order->getGiftCards());
        foreach ($giftCards as $code => $amount) {
            $giftCards[$code] = $amount * $rate;
        }

        $creditmemo->setGiftCards($giftCards);

        return $this;
    }
}
