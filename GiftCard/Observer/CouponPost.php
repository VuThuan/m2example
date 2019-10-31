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

use Exception;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Escaper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Bdcrops\GiftCard\Helper\Checkout as DataHelper;
use Bdcrops\GiftCard\Model\GiftCardFactory;

/**
 * Class CouponPost
 * @package Bdcrops\GiftCard\Observer
 */
class CouponPost implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    protected $_url;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @type GiftCardFactory
     */
    protected $_giftcardFactory;

    /**
     * @type DataHelper
     */
    protected $_dataHelper;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * CouponPost constructor.
     *
     * @param UrlInterface $url
     * @param Escaper $escaper
     * @param ManagerInterface $managerInterface
     * @param GiftCardFactory $giftcardFactory
     * @param DataHelper $dataHelper
     * @param CartRepositoryInterface $quoteRepository
     */
    function __construct(
        UrlInterface $url,
        Escaper $escaper,
        ManagerInterface $managerInterface,
        GiftCardFactory $giftcardFactory,
        DataHelper $dataHelper,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->_url = $url;
        $this->escaper = $escaper;
        $this->messageManager = $managerInterface;
        $this->_giftcardFactory = $giftcardFactory;
        $this->_dataHelper = $dataHelper;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     *
     * @return $this|Redirect
     */
    public function execute(Observer $observer)
    {
        if (!$this->_dataHelper->isEnabled() || !$this->_dataHelper->isUsedCouponBox()) {
            return $this;
        }

        /** @var \Magento\Checkout\Controller\Cart\CouponPost $action */
        $action = $observer->getEvent()->getControllerAction();

        /** @type RequestInterface $request */
        $request = $observer->getEvent()->getRequest();
        $couponCode = ($request->getParam('remove') == 1) ? '' : trim($request->getParam('coupon_code'));

        if (!strlen($couponCode)) {
            /** @var Quote $quote */
            $quote = $this->_dataHelper->getCheckoutSession()->getQuote();
            if ($quote->getCouponCode()) {
                return $this;
            }

            $giftCards = $this->_dataHelper->getGiftCardsUsed($quote);
            if ($giftCards && sizeof($giftCards)) {
                $this->_dataHelper->removeGiftCard(null, true);
                $this->messageManager->addSuccessMessage(__('You canceled the gift card code.'));

                return $this->_goBack($action);
            }

            return $this;
        }

        if ($this->_dataHelper->canUsedGiftCard()) {
            try {
                $this->_dataHelper->addGiftCards($couponCode);
                $this->messageManager->addSuccessMessage(__(
                    'You used gift card code "%1".',
                    $this->escaper->escapeHtml($couponCode)
                ));

                return $this->_goBack($action);
            } catch (Exception $e) {
                ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }

        return $this;
    }

    /**
     * @param \Magento\Checkout\Controller\Cart\CouponPost $action
     *
     * @return $this
     */
    protected function _goBack($action)
    {
        $action->getActionFlag()->set('', ActionInterface::FLAG_NO_DISPATCH, true);
        $action->getResponse()->setRedirect($this->_url->getUrl('checkout/cart'));

        return $this;
    }
}
