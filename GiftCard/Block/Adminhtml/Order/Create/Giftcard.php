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

namespace Bdcrops\GiftCard\Block\Adminhtml\Order\Create;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\Format;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate;
use Magento\Sales\Model\AdminOrder\Create;
use Bdcrops\GiftCard\Helper\Checkout as CheckoutHelper;
use Bdcrops\GiftCard\Model\ResourceModel\Transaction;

/**
 * Class Giftcard
 * @package Bdcrops\GiftCard\Block\Adminhtml\Order\Create
 */
class Giftcard extends AbstractCreate
{
    /**
     * @type CheckoutHelper
     */
    protected $_checkoutHelper;

    /**
     * @var Format
     */
    protected $_localeFormat;

    /**
     * @var int|double|float|null
     */
    protected $maxCreditUsed;

    /**
     * @var int|double|float|null
     */
    protected $customerBalance;

    /**
     * @var null|boolean
     */
    protected $canUseGiftCard = null;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Quote $sessionQuote
     * @param Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param CheckoutHelper $checkoutHelper
     * @param Transaction $resourceModel
     * @param Format $localeFormat
     * @param array $data
     */
    public function __construct(
        Context $context,
        Quote $sessionQuote,
        Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        CheckoutHelper $checkoutHelper,
        Transaction $resourceModel,
        Format $localeFormat,
        array $data = []
    ) {
        $this->_checkoutHelper = $checkoutHelper;
        $this->_localeFormat = $localeFormat;

        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_gift_card_coupons_form');
    }

    /**
     * @return bool|null
     */
    protected function canUseGiftCard()
    {
        if (is_null($this->canUseGiftCard)) {
            $this->canUseGiftCard = $this->_checkoutHelper->canUsedGiftCard($this->getQuote());
        }

        return $this->canUseGiftCard;
    }

    /**
     * @return bool
     */
    public function enableGiftCard()
    {
        return !$this->_checkoutHelper->isUsedCouponBox() && $this->canUseGiftCard();
    }

    /**
     * Enable Apply Credit
     *
     * @return bool
     */
    public function enableCredit()
    {
        return $this->_checkoutHelper->canUsedCredit() && $this->canUseGiftCard() && (boolean) $this->getMaxUsed();
    }

    /**
     * @return string
     */
    public function getBalanceFormatted()
    {
        return $this->formatPrice($this->getCustomerBalance());
    }

    /**
     * get credit amount data for slider
     *
     * @return string
     */
    public function getCreditData()
    {
        return $this->escapeHtml(CheckoutHelper::jsonEncode([
            'creditUsed'  => $this->_checkoutHelper->getGiftCreditUsed(),
            'maxUsed'     => $this->getMaxUsed(),
            'priceFormat' => $this->_localeFormat->getPriceFormat(null, $this->getQuote()->getQuoteCurrencyCode())
        ]));
    }

    /**
     * @return float|int|null|string
     * @throws NoSuchEntityException
     */
    protected function getCustomerBalance()
    {
        if (!$this->customerBalance) {
            $this->customerBalance = $this->_checkoutHelper->getCustomerBalance($this->getCustomerId());
        }

        return $this->customerBalance;
    }

    /**
     * @return float|int|mixed|null
     * @throws NoSuchEntityException
     */
    protected function getMaxUsed()
    {
        if (!$this->maxCreditUsed) {
            $balance = $this->getCustomerBalance();
            $orderTotal = $this->_checkoutHelper->getTotalAmountForDiscount($this->getQuote(), true);

            $this->maxCreditUsed = min($orderTotal, $balance);
        }

        return $this->maxCreditUsed;
    }

    /**
     * Get All codes applied
     *
     * @return array|null
     */
    public function getGiftCodesUsed()
    {
        return $this->_checkoutHelper->getGiftCardsUsed();
    }
}
