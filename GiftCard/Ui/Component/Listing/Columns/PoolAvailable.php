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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Bdcrops\GiftCard\Model\GiftCard\Status;
use Bdcrops\GiftCard\Model\GiftCardFactory;
use Bdcrops\GiftCard\Model\ResourceModel\GiftCard\Collection;

/**
 * Class PoolAvailable
 * @package Bdcrops\GiftCard\Ui\Component\Listing\Columns
 */
class PoolAvailable extends Column
{
    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * PoolAvailable constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param GiftCardFactory $giftCardFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        GiftCardFactory $giftCardFactory,
        array $components = [],
        array $data = []
    ) {
        $this->giftCardFactory = $giftCardFactory;

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
            foreach ($dataSource['data']['items'] as & $item) {
                $poolId = isset($item['pool_id']) ? $item['pool_id'] : null;

                /** @var Collection $collection */
                $collection = $this->giftCardFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('pool_id', $poolId);

                $totalSize = $collection->getSize();
                $collection->resetTotalRecords();

                $collection->addFieldToFilter('status', Status::STATUS_ACTIVE);
                $activeSize = $collection->getSize();

                $item[$this->getData('name')] = '<span style="font-weight: bold"><span style="color: forestgreen">' . $activeSize . '</span> / ' . $totalSize . '</span>';
            }
        }

        return $dataSource;
    }
}
