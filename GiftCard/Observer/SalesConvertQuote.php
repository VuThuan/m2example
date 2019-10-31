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
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Bdcrops\GiftCard\Helper\Data as DataHelper;
use Bdcrops\GiftCard\Model\GiftCardFactory;
use Bdcrops\GiftCard\Model\Transaction\Action;
use Bdcrops\GiftCard\Model\TransactionFactory;

/**
 * Class SalesConvertQuote
 * @package Bdcrops\GiftCard\Observer
 */
class SalesConvertQuote implements ObserverInterface
{
    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var DataHelper
     */
    protected $_helper;

    /**
     * SalesConvertQuote constructor.
     *
     * @param GiftCardFactory $giftCardFactory
     * @param TransactionFactory $transactionFactory
     * @param DataHelper $helper
     */
    public function __construct(
        GiftCardFactory $giftCardFactory,
        TransactionFactory $transactionFactory,
        DataHelper $helper
    ) {
        $this->giftCardFactory = $giftCardFactory;
        $this->transactionFactory = $transactionFactory;
        $this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     *
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();

        $giftCardsUsed = $quote->getGiftCards();
        if ($giftCardsUsed) {
            $giftCards = DataHelper::jsonDecode($giftCardsUsed);
            foreach ($giftCards as $code => $amount) {
                $this->giftCardFactory->create()
                    ->loadByCode($code)
                    ->spentForOrder($amount, $order);
            }

            $order->setGiftCards($giftCardsUsed);

            $order->setGiftCardAmount($address->getGiftCardAmount());
            $order->setBaseGiftCardAmount($address->getBaseGiftCardAmount());
        }

        $baseCreditAmount = $address->getBaseGiftCreditAmount();
        if (abs($baseCreditAmount) > 0.0001) {
            $order->setGiftCreditAmount($address->getGiftCreditAmount());
            $order->setBaseGiftCreditAmount($address->getBaseGiftCreditAmount());

            $this->transactionFactory->create()
                ->createTransaction(
                    Action::ACTION_SPEND,
                    $baseCreditAmount,
                    $order->getCustomerId(),
                    ['order_increment_id' => $order->getIncrementId()]
                );
        }
        $this->_helper->getCheckoutSession()->setGiftCardsData([]);

        return $this;
    }
}
