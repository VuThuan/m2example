<?php
namespace Bdcrops\CatalogRuleApi\Api;

use \Magento\Framework\Api\SearchCriteriaInterface;

interface CatalogRuleRepositoryInterface {
    /**
     * Get rules
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
     public function getList(
       \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

}
