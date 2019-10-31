<?php
/**
 * Bdcrops
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Bdcrops.com license sliderConfig is
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

namespace Bdcrops\GiftCard\Model\Api;

use Exception;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Bdcrops\GiftCard\Api\GiftCardManagementInterface;
use Bdcrops\GiftCard\Helper\Checkout as CheckoutHelper;
use Bdcrops\GiftCard\Model\GiftCardFactory;

/**
 * Class GiftCardManagement
 * @package Bdcrops\GiftCard\Model\Api
 */
class GiftCardManagement implements GiftCardManagementInterface
{
    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @type GiftCardFactory
     */
    protected $_giftCardFactory;

    /**
     * @type CheckoutHelper
     */
    protected $_checkoutHelper;

    /**
     * GiftCardManagement constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param GiftCardFactory $giftCardFactory
     * @param CheckoutHelper $checkoutHelper
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        GiftCardFactory $giftCardFactory,
        CheckoutHelper $checkoutHelper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->_checkoutHelper = $checkoutHelper;
        $this->_giftCardFactory = $giftCardFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $code)
    {
        /** @var  Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        try {
            $this->_checkoutHelper->addGiftCards($code, $quote);
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Could not apply gift card %1', $code));
        }

        $giftCardUsed = $this->_checkoutHelper->getGiftCardsUsed();
        if (!array_key_exists($code, $giftCardUsed)) {
            throw new NoSuchEntityException(__('Gift Card is not valid'));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId, $code)
    {
        /** @var  Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        $giftCard = $this->_giftCardFactory->create()->load($code);
        if (!$giftCard->getId()) {
            throw new CouldNotDeleteException(__('Could not cancel gift card'));
        }

        $code = $giftCard->getCode();

        $giftCardUsed = $this->_checkoutHelper->getGiftCardsUsed();
        if (!array_key_exists($code, $giftCardUsed)) {
            throw new NoSuchEntityException(__('Could not cancel gift card'));
        }

        try {
            $this->_checkoutHelper->removeGiftCard($code, false, $quote);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__('Could not cancel gift card'));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function credit($cartId, $amount)
    {
        /** @var  Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        try {
            $this->_checkoutHelper->applyCredit($amount, $quote->getCustomerId());
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Could not apply gift credit'));
        }

        return true;
    }
}
