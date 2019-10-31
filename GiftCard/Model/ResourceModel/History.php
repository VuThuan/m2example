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

namespace Bdcrops\GiftCard\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Bdcrops\GiftCard\Model\GiftCard\Action;

/**
 * Class History
 * @package Bdcrops\GiftCard\Model\ResourceModel
 */
class History extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('bdcrops_giftcard_history', 'history_id');
    }

    /**
     * @param $giftCards
     * @param $extraContent
     *
     * @throws LocalizedException
     */
    public function createMultiple($giftCards, $extraContent)
    {
        $data = [];
        foreach ($giftCards as $card) {
            $data[] = [
                'action'        => Action::ACTION_CREATE,
                'giftcard_id'   => $card->getId(),
                'code'          => $card->getCode(),
                'balance'       => $card->getBalance(),
                'amount'        => $card->getBalance(),
                'status'        => $card->getStatus(),
                'extra_content' => $extraContent
            ];
        }

        $this->getConnection()->insertMultiple($this->getMainTable(), $data);
    }
}
