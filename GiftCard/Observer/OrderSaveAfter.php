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
use Magento\Sales\Model\Order;
use Bdcrops\GiftCard\Helper\Product as Helper;
use Bdcrops\GiftCard\Model\Product\Type\GiftCard;

/**
 * Class OrderSaveAfter
 * @package Bdcrops\GiftCard\Observer
 */
class OrderSaveAfter implements ObserverInterface
{
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * OrderSaveAfter constructor.
     *
     * @param Helper $helper
     */
    public function __construct(Helper $helper)
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
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();
        if ($order->getState() == Order::STATE_COMPLETE) {
            foreach ($order->getAllItems() as $item) {
                if ($item->isDummy() || ($item->getProductType() !== GiftCard::TYPE_GIFTCARD)) {
                    continue;
                }

                $this->_helper->generateGiftCode($order, $item);
            }
        }

        return $this;
    }
}
