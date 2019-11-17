<?php
declare(strict_types=1);

namespace Bdcrops\GraphQLStorePickup\Model\ResourceModel;

use Bdcrops\GraphQLStorePickup\Model\ResourceModel\Store as StoreResourceModel;
use Bdcrops\GraphQLStorePickup\Model\Store as StoreModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class StoreCollection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(StoreModel::class, StoreResourceModel::class);
    }
}
