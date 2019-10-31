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

use Magento\Ui\Component\Listing\Columns\Column;
use Bdcrops\GiftCard\Helper\Data;
use Bdcrops\GiftCard\Model\GiftCard\Action;

/**
 * Class HistoryContent
 * @package Bdcrops\GiftCard\Ui\Component\Listing\Columns
 */
class HistoryContent extends Column
{
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
                $params = is_array($item['extra_content']) ? $item['extra_content'] : Data::jsonDecode($item['extra_content']);

                $item[$this->getData('name')] = Action::getActionLabel($item['action'], $params);
            }
        }

        return $dataSource;
    }
}
