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

namespace Bdcrops\GiftCard\Block\Adminhtml\Order\Items;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Registry;
use Magento\Sales\Block\Adminhtml\Items\Column\Name;
use Bdcrops\GiftCard\Helper\Product;
use Bdcrops\GiftCard\Model\GiftCard\Status;
use Bdcrops\GiftCard\Model\GiftCardFactory;

/**
 * Class Giftcard
 * @package Bdcrops\GiftCard\Block\Adminhtml\Order\Items
 */
class Giftcard extends Name
{
    /**
     * @var Product
     */
    protected $_gcHelper;

    /**
     * @var GiftCardFactory
     */
    protected $_giftCardFactory;

    /**
     * Giftcard constructor.
     *
     * @param Context $context
     * @param StockRegistryInterface $stockRegistry
     * @param StockConfigurationInterface $stockConfiguration
     * @param Registry $registry
     * @param OptionFactory $optionFactory
     * @param Product $gcProductHelper
     * @param GiftCardFactory $giftCardFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        StockRegistryInterface $stockRegistry,
        StockConfigurationInterface $stockConfiguration,
        Registry $registry,
        OptionFactory $optionFactory,
        Product $gcProductHelper,
        GiftCardFactory $giftCardFactory,
        array $data = []
    ) {
        $this->_gcHelper = $gcProductHelper;
        $this->_giftCardFactory = $giftCardFactory;

        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    /**
     * Return gift card and custom options array
     *
     * @return array
     */
    public function getOrderOptions()
    {
        $item = $this->getItem();

        $giftCardOptions = $this->_gcHelper->getOptionList($item, parent::getOrderOptions());

        $totalCodes = $item->getQtyOrdered() - $item->getQtyRefunded() - $item->getQtyCanceled();
        if ($totalCodes) {
            $giftCardCodes = [];
            $giftCards = $this->_giftCardFactory->create()->getCollection()
                ->addFieldToFilter(
                    'giftcard_id',
                    ['in' => $item->getProductOptionByCode('giftcards')]
                );
            foreach ($giftCards as $giftCard) {
                $code = $giftCard->getCode();
                if ($this->getRequest()->getFullActionName() == 'sales_order_creditmemo_new') {
                    if ($giftCard->getStatus() == Status::STATUS_CANCELLED) {
                        $code .= __(' (Cancelled)');
                    } elseif ($giftCard->getInitBalance() != $giftCard->getBalance()) {
                        $code .= __(' (Used)');
                    }
                }

                $giftCardCodes[] = $code;
            }
            for ($i = sizeof($giftCardCodes); $i < $totalCodes; $i++) {
                $giftCardCodes[] = __('N/A');
            }
            $giftCardOptions[] = [
                'label'       => __('Gift Codes'),
                'value'       => implode('<br />', $giftCardCodes),
                'custom_view' => true,
            ];
        }

        return $giftCardOptions;
    }
}
