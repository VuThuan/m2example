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

namespace Bdcrops\GiftCard\Pricing\Render;

use Exception;
use Magento\Catalog\Pricing\Render\FinalPriceBox as CatalogRender;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Bdcrops\GiftCard\Helper\Data;

/**
 * Class for final_price rendering
 */
class FinalPriceBox extends CatalogRender
{
    /**
     * @var AmountFactory
     */
    protected $amountFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Option amounts
     *
     * @var array
     */
    protected $_optionAmounts = [];

    /**
     * Min max values
     *
     * @var array
     */
    protected $_minMax = [];

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->findMinMaxValue();
    }

    /**
     * Amount Factory
     *
     * @return AmountFactory
     */
    public function getAmountFactory()
    {
        if (is_null($this->amountFactory)) {
            $this->amountFactory = ObjectManager::getInstance()->get(AmountFactory::class);
        }

        return $this->amountFactory;
    }

    /**
     * @return PriceCurrencyInterface|mixed
     */
    protected function getPriceCurrency()
    {
        if (is_null($this->amountFactory)) {
            $this->priceCurrency = ObjectManager::getInstance()->get(PriceCurrencyInterface::class);
        }

        return $this->priceCurrency;
    }

    /**
     * @return AmountInterface
     */
    public function getMinimalPrice()
    {
        $minimalPrice = $this->getPriceCurrency()->convert($this->_minMax['min']);

        return $this->getAmountFactory()->create($minimalPrice);
    }

    /**
     * @return AmountInterface
     */
    public function getMaximalPrice()
    {
        $maximalPrice = $this->getPriceCurrency()->convert($this->_minMax['max']);

        return $this->getAmountFactory()->create($maximalPrice);
    }

    /**
     * @return bool
     */
    public function isFixedPrice()
    {
        return !$this->isRangeAvailable() && (sizeof($this->getOptionPrices()) === 1);
    }

    /**
     * @return bool
     */
    public function isRangeAvailable()
    {
        return $this->saleableItem->getAllowAmountRange();
    }

    /**
     * @return array
     */
    public function getOptionPrices()
    {
        if (empty($this->_optionAmounts)) {
            try {
                $amountJson = $this->saleableItem->getGiftCardAmounts() ?: [];
                $amounts = is_string($amountJson) ? Data::jsonDecode($amountJson) : $amountJson;
            } catch (Exception $e) {
                $amounts = [];
            }

            $this->_optionAmounts;
            foreach ($amounts as $amount) {
                $this->_optionAmounts[] = $amount['price'];
            }
        }

        return $this->_optionAmounts;
    }

    /**
     * @return $this
     */
    protected function findMinMaxValue()
    {
        $min = $max = null;
        if ($this->isRangeAvailable()) {
            $rate = $this->saleableItem->getPriceRate() / 100;
            $min = ($this->saleableItem->getMinAmount() ?: 0) * $rate;
            $max = ($this->saleableItem->getMaxAmount() ?: 0) * $rate;
        }

        $minOp = $maxOp = null;
        $optionPrices = $this->getOptionPrices();
        if (sizeof($optionPrices)) {
            $minOp = min($optionPrices);
            $maxOp = max($optionPrices);
        }

        $this->_minMax = [
            'min' => ($min == null) ? $minOp : (($minOp == null) ? $min : min($min, $minOp)),
            'max' => ($max == null) ? $maxOp : (($maxOp == null) ? $max : max($max, $maxOp))
        ];

        return $this;
    }
}
