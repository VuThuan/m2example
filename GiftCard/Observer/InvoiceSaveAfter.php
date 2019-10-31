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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Item;
use Bdcrops\GiftCard\Helper\Product;
use Bdcrops\GiftCard\Model\Product\Type\GiftCard;
use Bdcrops\GiftCard\Model\Source\GenerateGiftCodeEvent;

/**
 * Class InvoiceSaveAfter
 * @package Bdcrops\GiftCard\Observer
 */
class InvoiceSaveAfter implements ObserverInterface
{
    /**
     * @var Product
     */
    protected $_helper;

    /**
     * InvoiceSaveAfter constructor.
     *
     * @param Product $helper
     */
    public function __construct(Product $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        if (!$this->_helper->isEnabled() ||
            !$this->_helper->isGenerateCode(GenerateGiftCodeEvent::INVOICED) ||
            $invoice->getState() !== Invoice::STATE_PAID
        ) {
            return $this;
        }

        /** @var \Magento\Sales\Model\Order\Invoice\Item $item */
        foreach ($invoice->getAllItems() as $item) {
            /** @var Item $orderItem */
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy() || ($orderItem->getProductType() != GiftCard::TYPE_GIFTCARD)) {
                continue;
            }

            $this->_helper->generateGiftCode($invoice->getOrder(), $orderItem, $item->getQty());
        }

        return $this;
    }
}
