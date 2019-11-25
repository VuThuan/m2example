<?php

declare(strict_types=1);

namespace Bdcrops\GraphQLStorePickup\Model\Resolver;

use Bdcrops\GraphQLStorePickup\Api\StoreRepositoryInterface;
use Bdcrops\GraphQLStorePickup\Model\Store\GetList;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class PickUpStores implements ResolverInterface {

    /**
     * @var GetListInterface
     */
    private $storeRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * PickUpStoresList constructor.
     * @param GetList $storeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(StoreRepositoryInterface $storeRepository, SearchCriteriaBuilder $searchCriteriaBuilder)
    {
        $this->storeRepository = $storeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {

        $this->vaildateArgs($args);

        $searchCriteria = $this->searchCriteriaBuilder->build('pickup_stores', $args);
        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);
        $searchResult = $this->storeRepository->getList($searchCriteria);

        return [
            'total_count' => $searchResult->getTotalCount(),
            'items' => $searchResult->getItems(),
        ];
    }

    /**
     * @param array $args
     * @throws GraphQlInputException
     */
    private function vaildateArgs(array $args): void
    {
        if (isset($args['currentPage']) && $args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }

        if (isset($args['pageSize']) && $args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }
    }
}
