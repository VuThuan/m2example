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
use Exception;
use Magento\Catalog\Model\Product\Attribute\Source\Boolean;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Bdcrops\GiftCard\Helper\Data;
use Bdcrops\GiftCard\Helper\Product;
use Bdcrops\GiftCard\Helper\Template;
use Bdcrops\GiftCard\Model\Source\FieldRenderer;
use Psr\Log\LoggerInterface;

/**
 * Class ToOrderItem
 * @package Bdcrops\GiftCard\Plugin\Quote
 */
class ToOrderItem
{
    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * ToOrderItem constructor.
     *
     * @param Data $dataHelper
     */
    public function __construct(Data $dataHelper)
    {
        $this->_dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param Closure $proceed
     * @param AbstractItem $item
     * @param                                              $additional
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        Closure $proceed,
        AbstractItem $item,
        $additional
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $item->getProduct();

        $productOptions = $orderItem->getProductOptions();
        foreach (FieldRenderer::getFullOptionArray() as $key => $label) {
            $option = $product->getCustomOption($key);
            if ($option) {
                $productOptions[$key] = $option->getValue();
            }
        }

        $canRedeem = $product->getCanRedeem();
        if ($canRedeem == Boolean::VALUE_USE_CONFIG) {
            $canRedeem = $this->_dataHelper->getGeneralConfig('can_redeem', $orderItem->getStore());
        }
        $productOptions['can_redeem'] = $canRedeem;

        $expiredDay = $product->getExpireAfterDay();
        if ($expiredDay == Product::VALUE_USE_CONFIG) {
            $expiredDay = $this->_dataHelper->getProductConfig('expire_after_day', $orderItem->getStore());
        }
        $productOptions['expire_after'] = $expiredDay;

        $pattern = $product->getGiftCodePattern();
        if ($pattern == Product::VALUE_USE_CONFIG) {
            $pattern = $this->_dataHelper->getGeneralConfig('pattern', $orderItem->getStore());
        }
        $productOptions['pattern'] = $pattern;

        $productOptions['giftcard_type'] = $product->getGiftCardType();

        //Copy images uploaded
        if (isset($productOptions['image'])) {
            $image = $productOptions['image'];

            $pos = strpos($image, '.tmp');
            if ($pos != false) {
                unset($productOptions['image']);

                /** @var Template $templateHelper */
                $templateHelper = $this->_dataHelper->getTemplateHelper();

                /** @var WriteInterface $mediaDirectory */
                $mediaDirectory = $templateHelper->getMediaDirectory();

                $fileName = substr($image, 0, $pos);
                $filePath = $templateHelper->getTmpMediaPath($fileName);
                $file = $mediaDirectory->getRelativePath($filePath);
                if ($mediaDirectory->isFile($file)) {
                    $pathInfo = pathinfo($file);
                    $fileName = Uploader::getCorrectFileName($pathInfo['basename']);
                    $dispretionPath = Uploader::getDispretionPath($fileName);
                    $fileName = $dispretionPath . '/' . $fileName;

                    $fileName = $templateHelper->getNotDuplicatedFilename($fileName, $dispretionPath);
                    $destinationFile = $templateHelper->getMediaPath($fileName);

                    try {
                        $mediaDirectory->renameFile($file, $destinationFile);
                        $productOptions['image'] = str_replace('\\', '/', $fileName);
                    } catch (Exception $e) {
                        ObjectManager::getInstance()->get(LoggerInterface::class)
                            ->critical($e->getMessage());
                    }
                }
            }
        }

        $orderItem->setProductOptions($productOptions);

        return $orderItem;
    }
}
