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

use Exception;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Transaction
 * @package Bdcrops\GiftCard\Model
 */
class Transaction extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('bdcrops_giftcard_transaction', 'transaction_id');
    }

    /**
     * @param $objects
     *
     * @return $this
     * @throws Exception
     */
    public function createTransaction($objects)
    {
        $this->beginTransaction();

        try {
            foreach ($objects as $object) {
                $object->save();
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }
}
