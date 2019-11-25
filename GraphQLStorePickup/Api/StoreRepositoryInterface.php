<?php
declare(strict_types=1);

namespace Bdcrops\GraphQLStorePickup\Api;

use Bdcrops\GraphQLStorePickup\Api\Data\StoreInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface StoreRepositoryInterface {
    /**
     * Save the Store data.
     *
     * @param \Magento\InventoryApi\Api\Data\SourceInterface $source
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(StoreInterface $store): void;

    /**
     * Find Stores by given SearchCriteria
     * SearchCriteria is not required because load all stores is useful case
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): SearchResultsInterface;
}
