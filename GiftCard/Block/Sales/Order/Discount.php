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

namespace Bdcrops\GiftCard\Block\Sales\Order;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;

/**
 * Class Discount
 * @package Bdcrops\GiftCard\Block\Sales\Order
 */
class Discount extends Template
{
    /**
     * Add gift card discount total
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $source = $parent->getSource();

        if (abs($source->getGiftCardAmount()) > 0.001) {
            $parent->addTotal(new DataObject(
                [
                    'code'       => 'gift_card',
                    'value'      => $source->getGiftCardAmount(),
                    'base_value' => $source->getBaseGiftCardAmount(),
                    'label'      => __('Gift Cards')
                ]
            ), 'tax');
        }

        if (abs($source->getGiftCreditAmount()) > 0.001) {
            $parent->addTotal(new DataObject(
                [
                    'code'       => 'gift_credit',
                    'value'      => $source->getGiftCreditAmount(),
                    'base_value' => $source->getBaseGiftCreditAmount(),
                    'label'      => __('Gift Credit')
                ]
            ), 'tax');
        }

        return $this;
    }
}
