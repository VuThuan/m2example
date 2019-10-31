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

namespace Bdcrops\GiftCard\Helper;

use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;
use Bdcrops\GiftCard\Model\GiftCardFactory;
use Bdcrops\GiftCard\Model\Product\Type\GiftCard;

/**
 * Class Checkout
 * @package Bdcrops\GiftCard\Helper
 */
class Checkout extends Data
{
    /**
     * @var CartRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * @type GiftCardFactory
     */
    protected $_giftCardFactory;

    /**
     * Checkout constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $localeDate
     * @param CartRepositoryInterface $quoteRepository
     * @param GiftCardFactory $giftCardFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        CartRepositoryInterface $quoteRepository,
        GiftCardFactory $giftCardFactory
    ) {
        $this->_quoteRepository = $quoteRepository;
        $this->_giftCardFactory = $giftCardFactory;

        parent::__construct($context, $objectManager, $storeManager, $localeDate);
    }

    /**
     * Get all gift card data
     *
     * @return mixed
     */
    public function getGiftCardsData()
    {
        $giftCardsData = $this->getCheckoutSession()->getGiftCardsData();
        if (!is_array($giftCardsData)) {
            $giftCardsData = [];
        }

        return $giftCardsData;
    }

    /**
     * save gift card to session
     * $giftCardsData [
     *        'gift_cards' => [
     *            'CODE' => AMOUNT
     *        ],
     *        'gift_credit' => AMOUNT,
     *        'base_gift_card_amount' => AMOUNT,
     *        'gift_card_amount' => AMOUNT,
     *        'base_gift_credit_amount' => AMOUNT,
     *        'gift_credit_amount' => AMOUNT
     * ]
     *
     * @param $giftCards
     *
     * @return $this
     */
    public function saveGiftCardsData($giftCards)
    {
        $giftCardsData = $this->getGiftCardsData();
        $this->getCheckoutSession()->setGiftCardsData(array_merge($giftCardsData, $giftCards));

        return $this;
    }

    /**
     * Collect and save total
     *
     * @param null $quote
     *
     * @return $this
     */
    protected function collectTotals($quote = null)
    {
        if ($this->isAdmin()) {
            return $this;
        }

        if (is_null($quote)) {
            /** @var Quote $quote */
            $quote = $this->getCheckoutSession()->getQuote();
        }

        $quote->getShippingAddress()->setCollectShippingRates(true);

        $this->_quoteRepository->save($quote->collectTotals());

        return $this;
    }


    /******************************************* Gift Card **********************************************/

    /**
     * Get gift card used
     *
     * @param null $quote
     *
     * @return array|mixed
     */
    public function getGiftCardsUsed($quote = null)
    {
        $giftCardsData = $this->getGiftCardsData();
        if ($giftCardsData && isset($giftCardsData['gift_cards'])) {
            return $giftCardsData['gift_cards'];
        }

        if (is_null($quote)) {
            $quote = $this->getCheckoutSession()->getQuote();
        }
        $savedGiftCard = $quote->getGiftCards();
        try {
            $savedGiftCard = (!is_null($savedGiftCard)) ? self::jsonDecode($savedGiftCard) : [];
        } catch (Exception $e) {
            $savedGiftCard = [];
        }

        return $savedGiftCard;
    }

    /**
     * Get gift card used
     *
     * @return mixed
     */
    public function getGiftCardAmountUsed()
    {
        $giftCardsData = $this->getGiftCardsData();
        if ($giftCardsData && isset($giftCardsData['base_gift_card_amount'])) {
            return $giftCardsData['base_gift_card_amount'];
        }

        return 0;
    }

    /**
     * Check quote can used gift card or not
     *
     * @param Quote $quote
     *
     * @return bool
     */
    public function canUsedGiftCard($quote = null)
    {
        if (is_null($quote)) {
            $quote = $this->getCheckoutSession()->getQuote();
        }

        if (!$this->isEnabled($quote->getStoreId())) {
            return false;
        }

        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            if ($item->getProductType() != GiftCard::TYPE_GIFTCARD) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param      $codes
     * @param null $quote
     *
     * @return $this
     */
    public function setGiftCards($codes, $quote = null)
    {
        if (!is_array($codes)) {
            $codes = [$codes];
        }

        $giftCards = [];
        foreach ($codes as $code) {
            $giftCards[$code] = 0;
        }

        $this->saveGiftCardsData(['gift_cards' => $giftCards]);
        $this->collectTotals($quote);

        return $this;
    }

    /**
     * @param $codes
     * @param null $quote
     *
     * @return $this
     * @throws LocalizedException
     * @throws Exception
     */
    public function addGiftCards($codes, $quote = null)
    {
        if (!is_array($codes)) {
            $codes = [$codes];
        }

        foreach ($codes as $code) {
            /** @var \Bdcrops\GiftCard\Model\GiftCard $giftCard */
            $giftCard = $this->_giftCardFactory->create();
            $giftCard->loadByCode($code);
            if (!$giftCard->isActive()) {
                throw new LocalizedException(__('The gift card code "%1" is not valid.', $code));
            }
        }

        if (is_null($quote)) {
            $quote = $this->getCheckoutSession()->getQuote();
        }

        $store = $quote->getStore();
        if ($this->isUsedMultipleCode($store)) {
            $giftCardsUsed = array_keys($this->getGiftCardsUsed());
            $codes = array_unique(array_merge($giftCardsUsed, $codes));
        } elseif (sizeof($codes) > 1) {
            $codes = [array_shift($codes)];
        }

        $this->setGiftCards($codes, $quote);

        return $this;
    }

    /**
     * Remove gift card code from session
     *
     * @param      $code
     * @param bool $removeAll
     * @param null $quote
     *
     * @return $this
     */
    public function removeGiftCard($code, $removeAll = false, $quote = null)
    {
        if ($removeAll) {
            $this->setGiftCards([], $quote);

            return $this;
        }

        $giftCards = $this->getGiftCardsUsed($quote);
        if (array_key_exists($code, $giftCards)) {
            unset($giftCards[$code]);
            $this->setGiftCards(array_keys($giftCards), $quote);
        }

        return $this;
    }

    /******************************************* Gift Credit **********************************************/

    /**
     * Get gift card used
     *
     * @return mixed
     */
    public function getGiftCreditUsed()
    {
        $giftCardsData = $this->getGiftCardsData();
        if ($giftCardsData && isset($giftCardsData['gift_credit'])) {
            return $giftCardsData['gift_credit'];
        }

        return 0;
    }

    /**
     * Apply Credit
     *
     * @param      $amount
     * @param null $customer
     *
     * @return $this
     * @throws LocalizedException
     */
    public function applyCredit($amount, $customer = null)
    {
        $balance = $this->getCustomerBalance($customer);
        if ($amount < 0 || $amount > $balance) {
            throw new LocalizedException(__('Invalid credit amount.'));
        }

        $this->saveGiftCardsData(['gift_credit' => $amount]);
        $this->collectTotals();

        return $this;
    }

    /********************************************** Calculation *****************************************/

    /**
     * Calculate total amount for discount
     *
     * @param Quote $quote
     * @param bool $isCredit
     *
     * @return float|mixed
     */
    public function getTotalAmountForDiscount(Quote $quote, $isCredit = false)
    {
        $discountTotal = $quote->getBaseGrandTotal();
        if (!$quote->isVirtual() && !$this->canUsedForShipping($quote->getStoreId())) {
            $discountTotal -= $quote->getShippingAddress()->getBaseShippingAmount();
        }

        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            // todo use configuration to select which type of product can be spent by gift card
            if ($item->getProductType() == GiftCard::TYPE_GIFTCARD) {
                $discountTotal -= $item->getBaseRowTotal();
            }
        }

        if ($isCredit) {
            $discountTotal += $this->getGiftCreditUsed();
        }

        return $discountTotal;
    }
}
