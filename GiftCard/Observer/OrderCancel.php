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
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Bdcrops\GiftCard\Helper\Data as Helper;
use Bdcrops\GiftCard\Model\GiftCard\Action;
use Bdcrops\GiftCard\Model\GiftCardFactory;
use Bdcrops\GiftCard\Model\TransactionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class OrderCancel
 * @package Bdcrops\GiftCard\Observer
 */
class OrderCancel implements ObserverInterface
{
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * OrderCancel constructor.
     *
     * @param Helper $helper
     * @param GiftCardFactory $giftCardFactory
     * @param TransactionFactory $transactionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Helper $helper,
        GiftCardFactory $giftCardFactory,
        TransactionFactory $transactionFactory,
        LoggerInterface $logger
    ) {
        $this->_helper = $helper;
        $this->giftCardFactory = $giftCardFactory;
        $this->transactionFactory = $transactionFactory;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        $giftCards = $order->getGiftCards() ? Helper::jsonDecode($order->getGiftCards()) : [];
        foreach ($giftCards as $code => $amount) {
            try {
                $giftCard = $this->giftCardFactory->create()->loadByCode($code);
                if ($giftCard->getId()) {
                    $giftCard->addBalance($amount)
                        ->setAction(Action::ACTION_REVERT)
                        ->setActionVars(['order_increment_id' => $order->getIncrementId()])
                        ->save();
                }
            } catch (Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }

        $giftCredit = $order->getBaseGiftCreditAmount();
        if (abs($giftCredit) > 0.0001) {
            try {
                $this->transactionFactory->create()
                    ->createTransaction(
                        \Bdcrops\GiftCard\Model\Transaction\Action::ACTION_REVERT,
                        $giftCredit,
                        $order->getCustomerId(),
                        ['order_increment_id' => $order->getIncrementId()]
                    );
            } catch (Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }

        return $this;
    }
}
