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

namespace Bdcrops\GiftCard\Plugin\Quote;

use Magento\Sales\Model\Order\Item as OrderItem;
use Bdcrops\GiftCard\Model\GiftCardFactory;

/**
 * Class ToOrderItem
 * @package Bdcrops\GiftCard\Plugin\Quote
 */
class Item
{
    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * Item constructor.
     *
     * @param GiftCardFactory $giftCardFactory
     */
    public function __construct(GiftCardFactory $giftCardFactory)
    {
        $this->giftCardFactory = $giftCardFactory;
    }

    /**
     * @param OrderItem $item
     * @param           $result
     *
     * @return mixed
     */
    public function afterGetQtyToRefund(OrderItem $item, $result)
    {
        if ($item->getProductType() == 'mpgiftcard') {
            $options = $item->getProductOptions();
            $options['refundable_gift_card'] = [];
            $giftCards = $this->giftCardFactory->create()->getCollection()
                ->addFieldToFilter('giftcard_id', ['in' => $item->getProductOptionByCode('giftcards')])
                ->addFieldToFilter('status', ['neq' => 6]);
            $count = 0;
            foreach ($giftCards as $giftCard) {
                if ($giftCard->getInitBalance() != $giftCard->getBalance()) {
                    $count += 1;
                } else {
                    $options['refundable_gift_card'][] = $giftCard->getId();
                }
            }

            $item->setProductOptions($options);

            return max($item->getQtyInvoiced() - $item->getQtyRefunded() - $count, 0);
        }

        return $result;
    }
}
