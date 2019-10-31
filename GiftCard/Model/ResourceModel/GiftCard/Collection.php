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

namespace Bdcrops\GiftCard\Model\ResourceModel\GiftCard;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Bdcrops\GiftCard\Model\ResourceModel\GiftCard
 */
class Collection extends AbstractCollection
{
    /**
     * @type string
     */
    protected $_idFieldName = 'giftcard_id';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('Bdcrops\GiftCard\Model\GiftCard', 'Bdcrops\GiftCard\Model\ResourceModel\GiftCard');
    }

    /**
     * @inheritdoc
     */
    public function resetTotalRecords()
    {
        $this->_totalRecords = null;
    }
}
