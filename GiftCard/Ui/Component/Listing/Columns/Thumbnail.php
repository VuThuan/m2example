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

namespace Bdcrops\GiftCard\Ui\Component\Listing\Columns;

use Magento\Catalog\Helper\Image;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Bdcrops\GiftCard\Helper\Template;

/**
 * Class Thumbnail
 * @package Bdcrops\GiftCard\Ui\Component\Listing\Columns
 */
class Thumbnail extends Column
{
    const NAME      = 'thumbnail';
    const ALT_FIELD = 'name';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var Template
     */
    protected $templateHelper;

    /**
     * Thumbnail constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param Image $imageHelper
     * @param Template $templateHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        Image $imageHelper,
        Template $templateHelper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->templateHelper = $templateHelper;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$fieldName . '_alt'] = __('Gift Card');
                $item[$fieldName . '_src'] = $this->templateHelper->getPlaceHolderImage();

                $templateFields = $this->templateHelper->prepareTemplateData($item, true, true);
                if (isset($templateFields['design'])) {
                    $item[$fieldName . '_src'] = sizeof($templateFields['images']) ? $templateFields['images'][0]['src'] : $this->templateHelper->getPlaceHolderImage();
                    $item[$fieldName . '_images'] = array_column($templateFields['images'], 'src');
                    $item[$fieldName . '_card'] = $templateFields['card'];
                    $item[$fieldName . '_design'] = $templateFields['design'];
                }
            }
        }

        return $dataSource;
    }
}
