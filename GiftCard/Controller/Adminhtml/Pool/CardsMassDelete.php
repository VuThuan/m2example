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

namespace Bdcrops\GiftCard\Controller\Adminhtml\Pool;

use Bdcrops\GiftCard\Controller\Adminhtml\Pool;

/**
 * Class CardsMassDelete
 * @package Bdcrops\GiftCard\Controller\Adminhtml\Pool
 */
class CardsMassDelete extends Pool
{
    /**
     * Coupons mass delete action
     *
     * @return void
     */
    public function execute()
    {
        $pool = $this->_initObject();

        if (!$pool->getId()) {
            $this->_forward('noroute');
        }

        $codesIds = $this->getRequest()->getParam('ids');

        if (is_array($codesIds)) {
            $collection = $this->_objectManager->create('Bdcrops\GiftCard\Model\ResourceModel\GiftCard\Collection')
                ->addFieldToFilter('giftcard_id', ['in' => $codesIds])
                ->addFieldToFilter('pool_id', $pool->getId());

            foreach ($collection as $giftCard) {
                $giftCard->delete();
            }
        }
    }
}
