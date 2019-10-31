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

namespace Bdcrops\GiftCard\Model\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\Price as CatalogPrice;

/**
 * Class Price
 * @package Bdcrops\GiftCard\Model\Product
 */
class Price extends CatalogPrice
{
    /**
     * @param float|null $qty
     * @param Product $product
     *
     * @return float|int|mixed
     */
    public function getFinalPrice($qty, $product)
    {
        if (is_null($qty) && $product->getCalculatedFinalPrice() !== null) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = $this->getPrice($product);
        if ($product->hasCustomOptions()) {
            $amount = $product->getCustomOption('amount');
            $amount = $amount ? $amount->getValue() : 0;

            $rangeAmount = $product->getCustomOption('range_amount');
            if ($rangeAmount && $rangeAmount->getValue()) {
                $priceRate = $product->getPriceRate() ?: 100;
                $finalPrice = $amount * $priceRate / 100;
            } else {
                $attribute = $product->getResource()->getAttribute('gift_card_amounts');
                $attribute->getBackend()->afterLoad($product);
                $allowAmounts = $product->getGiftCardAmounts();

                foreach ($allowAmounts as $amountValue) {
                    if ($amountValue['amount'] == $amount) {
                        $finalPrice = $amountValue['price'];
                        break;
                    }
                }
            }
        }
        $product->setFinalPrice($finalPrice);

        $this->_eventManager->dispatch('catalog_product_get_final_price', ['product' => $product, 'qty' => $qty]);

        $finalPrice = $product->getData('final_price');
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        $finalPrice = max(0, $finalPrice);
        $product->setFinalPrice($finalPrice);

        return $finalPrice;
    }
}
