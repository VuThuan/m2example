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

namespace Bdcrops\GiftCard\Block\Product;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Catalog\Model\Product;
use Magento\Config\Model\Config\Source\Locale\Timezone;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\ArrayUtils;
use Bdcrops\GiftCard\Helper\Data;
use Bdcrops\GiftCard\Helper\Template;
use Bdcrops\GiftCard\Model\Product\DeliveryMethods;
use Bdcrops\GiftCard\Model\Source\Status;
use Bdcrops\GiftCard\Model\TemplateFactory;

/**
 * Class View
 * @package Bdcrops\GiftCard\Block\Product
 */
class View extends AbstractView
{
    /**
     * @var array
     */
    protected $_templates = [];

    /**
     * @var Template
     */
    protected $templateHelper;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var LocaleFormat
     */
    protected $localeFormat;

    /**
     * @var Timezone
     */
    protected $timezoneSource;

    /**
     * @var DeliveryMethods
     */
    protected $deliveryMethods;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param TemplateFactory $templateFactory
     * @param Data $dataHelper
     * @param PriceCurrencyInterface $priceCurrency
     * @param LocaleFormat $localeFormat
     * @param Timezone $timezoneSource
     * @param DeliveryMethods $deliveryMethods
     * @param array $data
     */
    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        TemplateFactory $templateFactory,
        Data $dataHelper,
        PriceCurrencyInterface $priceCurrency,
        LocaleFormat $localeFormat,
        Timezone $timezoneSource,
        DeliveryMethods $deliveryMethods,
        array $data = []
    ) {
        $this->templateFactory = $templateFactory;
        $this->dataHelper = $dataHelper;
        $this->templateHelper = $dataHelper->getTemplateHelper();
        $this->priceCurrency = $priceCurrency;
        $this->localeFormat = $localeFormat;
        $this->timezoneSource = $timezoneSource;
        $this->deliveryMethods = $deliveryMethods;

        parent::__construct($context, $arrayUtils, $data);

        $this->_templates = $this->initTemplates();
    }

    /**
     * @return int
     */
    public function isUseTemplate()
    {
        return sizeof($this->getTemplates());
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        return $this->_templates;
    }

    /**
     * @return array
     */
    public function getProductConfig()
    {
        return [
            'information' => $this->prepareInformation(),
            'template'    => $this->_templates
        ];
    }

    /**
     * @return array
     */
    public function prepareInformation()
    {
        $product = $this->getProduct();
        $deliveryParam = $product->getConfigureMode()
            ? $product->getPreconfiguredValues()->getData()
            : [];

        $enableDeliveryDate = ($product->getGiftCardType() != DeliveryMethods::TYPE_PRINT) && $this->dataHelper->getProductConfig('enable_delivery_date');

        $information = [
            'productId'          => $product->getId(),
            'currencyRate'       => $this->priceCurrency->convert(1),
            'priceFormat'        => $this->localeFormat->getPriceFormat(),
            'amounts'            => $product->getGiftCardAmounts() ?: [],
            'delivery'           => $this->deliveryMethods->getDeliveryMethod(
                $product->getGiftCardType(),
                $deliveryParam
            ),
            'enableDeliveryDate' => $enableDeliveryDate,
            'timezone'           => [
                'enable'  => $enableDeliveryDate && $this->dataHelper->getProductConfig('enable_timezone'),
                'options' => $this->timezoneSource->toOptionArray(),
                'value'   => $this->dataHelper->getConfigValue('general/locale/timezone')
            ],
            'fileUploadUrl'      => $this->_urlBuilder->getUrl('mpgiftcard/template/upload'),
            'messageMaxChar'     => $this->dataHelper->getMessageMaxChar()
        ];

        if ((boolean) $product->getAllowAmountRange()) {
            $minAmount = $product->getMinAmount();
            $minAmount = (!$minAmount || $minAmount < 0) ? 0 : $minAmount;

            $maxAmount = $product->getMaxAmount();
            $maxAmount = ($maxAmount && $maxAmount < $minAmount) ? $minAmount : $maxAmount;

            $priceRate = $product->getPriceRate() ?: 100;

            $information['openAmount'] = [
                'min'  => $minAmount,
                'max'  => $maxAmount,
                'rate' => $priceRate,
            ];
        }

        return $information;
    }

    /**
     * @return array
     */
    public function initTemplates()
    {
        $resultTemplates = [];
        $templateIds = $this->getProduct()->getGiftProductTemplate();
        if ($templateIds) {
            $templates = $this->templateFactory->create()
                ->getCollection()
                ->addFieldToFilter('template_id', ['in' => explode(',', $templateIds)])
                ->addFieldToFilter('status', Status::STATUS_ACTIVE);
            foreach ($templates as $template) {
                $resultTemplates[$template->getId()] = $this->templateHelper->prepareTemplateData($template->getData());
            }
        }

        return $resultTemplates;
    }

    /**
     * @return array|mixed
     * @throws NoSuchEntityException
     */
    public function getConfigureData()
    {
        $configureData = [];
        /** @var Product $product */
        $product = $this->getProduct();
        if ($product->getConfigureMode()) {
            $configureData = $product->getPreconfiguredValues()->getData();
        }

        if (($customer = $this->dataHelper->getCustomer()) && !isset($configureData['from'])) {
            $configureData['from'] = $customer->getName();
        }

        return $configureData;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->addPageAsset('jquery/fileUploader/css/jquery.fileupload-ui.css');

        return parent::_prepareLayout();
    }

    /**
     * @return bool|string
     */
    public function getFonts()
    {
        $fonts = '';

        foreach ($this->_templates as $template) {
            if (!in_array($template['font'], ['Arial', 'times', 'helvetica', 'courier'])) {
                $fonts .= $template['font'] . '|';
            }
        }

        return substr($fonts, 0, -1);
    }
}
