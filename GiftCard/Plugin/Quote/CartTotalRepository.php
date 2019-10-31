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

use Closure;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\TotalsExtensionFactory;
use Magento\Quote\Model\Quote;
use Bdcrops\GiftCard\Helper\Checkout;
use Bdcrops\GiftCard\Model\GiftCard\Status;
use Bdcrops\GiftCard\Model\GiftCardFactory;

/**
 * Class CartTotalRepository
 * @package Bdcrops\GiftCard\Plugin\Quote
 */
class CartTotalRepository
{
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var TotalsExtensionFactory
     */
    protected $totalExtensionFactory;

    /**
     * @var Checkout
     */
    protected $checkoutHelper;

    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Asset service
     *
     * @var Repository
     */
    protected $_assetRepo;

    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * CartTotalRepository constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param TotalsExtensionFactory $totalExtensionFactory
     * @param Checkout $gcCheckoutHelper
     * @param GiftCardFactory $giftCardFactory
     * @param RequestInterface $request
     * @param Repository $assetRepo
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        TotalsExtensionFactory $totalExtensionFactory,
        Checkout $gcCheckoutHelper,
        GiftCardFactory $giftCardFactory,
        RequestInterface $request,
        Repository $assetRepo,
        UrlInterface $urlBuilder
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->totalExtensionFactory = $totalExtensionFactory;
        $this->checkoutHelper = $gcCheckoutHelper;
        $this->giftCardFactory = $giftCardFactory;
        $this->request = $request;
        $this->_assetRepo = $assetRepo;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * @param CartTotalRepositoryInterface $subject
     * @param Closure $proceed
     * @param $cartId
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function aroundGet(CartTotalRepositoryInterface $subject, Closure $proceed, $cartId)
    {
        $quoteTotals = $proceed($cartId);

        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!($enableGiftCard = $this->checkoutHelper->canUsedGiftCard($quote))) {
            return $quoteTotals;
        }

        $customerBalance = $this->checkoutHelper->getCustomerBalance($quote->getCustomerId(), true);
        $maxUsed = min($customerBalance, $this->checkoutHelper->getTotalAmountForDiscount($quote, true));
        $giftCardConfig = [
            'enableGiftCard'   => !$this->checkoutHelper->isUsedCouponBox() && $enableGiftCard,
            'enableMultiple'   => $this->checkoutHelper->isUsedMultipleCode(),
            'canShowDetail'    => (boolean) $this->checkoutHelper->getCheckoutConfig('show_detail'),
            'listGiftCard'     => $this->getGiftCardList(),
            'giftCardUsed'     => $this->getGiftCardsUsed($quote),
            'enableGiftCredit' => $this->checkoutHelper->canUsedCredit() && $enableGiftCard && ($maxUsed > 0.0001),
            'balance'          => $customerBalance,
            'maxUsed'          => $maxUsed,
            'creditUsed'       => $this->checkoutHelper->getGiftCreditUsed(),
            'css'              => [
                $this->getViewFileUrl('Bdcrops_Core/css/ion.rangeSlider.css'),
                $this->getViewFileUrl('Bdcrops_Core/css/skin/ion.rangeSlider.skinModern.css')
            ]
        ];

        $totalsExtension = $quoteTotals->getExtensionAttributes() ?: $this->totalExtensionFactory->create();
        $totalsExtension->setGiftCards(Checkout::jsonEncode($giftCardConfig));

        $quoteTotals->setExtensionAttributes($totalsExtension);

        return $quoteTotals;
    }

    /**
     * @param $quote
     *
     * @return array
     */
    public function getGiftCardsUsed($quote)
    {
        $giftCardUsed = $this->checkoutHelper->getGiftCardsUsed($quote);

        $result = [];
        foreach ($giftCardUsed as $code => $amount) {
            $giftCard = $this->giftCardFactory->create()
                ->loadByCode($code);
            if ($id = $giftCard->getId()) {
                $result[$id] = [
                    'code'   => $this->checkoutHelper->isUsedCouponBox() ? $giftCard->getCode() : $giftCard->getHiddenCode(),
                    'amount' => $amount
                ];
            }
        }

        return $result;
    }

    /**
     * Get Gift Card List
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getGiftCardList()
    {
        $listResult = [];

        $customer = $this->checkoutHelper->getCustomer();
        if ($customer && $customer->getId()) {
            $giftCardList = $this->giftCardFactory->create()
                ->getGiftCardListForCustomer($this->checkoutHelper->getCustomer()->getId());
            foreach ($giftCardList as $giftCard) {
                if ($giftCard['status'] != Status::STATUS_ACTIVE) {
                    continue;
                }

                $listResult[$giftCard['giftcard_id']] = [
                    'code'        => $giftCard['code'],
                    'hidden_code' => $giftCard['hidden_code'],
                    'balance'     => $this->checkoutHelper->convertPrice($giftCard['balance'], true, false)
                ];
            }
        }

        return $listResult;
    }

    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @param array $params
     *
     * @return string
     */
    public function getViewFileUrl($fileId, array $params = [])
    {
        try {
            $params = array_merge(['_secure' => $this->request->isSecure()], $params);

            return $this->_assetRepo->getUrlWithParams($fileId, $params);
        } catch (LocalizedException $e) {
            return $this->_urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']);
        }
    }
}
