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

namespace Bdcrops\GiftCard\Block\Sales\Item;

use Magento\Framework\App\ObjectManager;
use Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer;
use Magento\Sales\Model\Order\Item;
use Bdcrops\GiftCard\Helper\Product;

/**
 * Class Renderer
 * @package Bdcrops\GiftCard\Block\Sales\Item
 */
class Renderer extends DefaultRenderer
{
    /**
     * Return gift card and custom options array
     *
     * @return array
     */
    public function getItemOptions()
    {
        $helper = ObjectManager::getInstance()->get(Product::class);

        /** @var Item $item */
        $item = $this->getOrderItem();

        $itemOptions = $helper->getOptionList($item, parent::getItemOptions());

        $totalCodes = $item->getQtyOrdered() - $item->getQtyRefunded() - $item->getQtyCanceled();
        if ($totalCodes) {
            $giftCardCodes = $helper->getGiftCardCodesFromIds($item->getProductOptionByCode('giftcards') ?: []);
            for ($i = sizeof($giftCardCodes); $i < $totalCodes; $i++) {
                $giftCardCodes[] = __('N/A');
            }

            $itemOptions[] = [
                'label'       => __('Gift Codes'),
                'value'       => implode('<br />', $giftCardCodes),
                'custom_view' => true,
            ];
        }

        return $itemOptions;
    }
}
