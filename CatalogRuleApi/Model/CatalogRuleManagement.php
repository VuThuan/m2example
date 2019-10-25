<?php

namespace Bdcrops\CatalogRuleApi\Model;

use \Magento\CatalogRule\Model\RuleFactory;
use \Bdcrops\CatalogRuleApi\Api\CatalogRuleRepositoryInterface;

class CatalogRuleManagement implements CatalogRuleRepositoryInterface {
    /**
     * @var RuleFactory
     */
    protected $_catalogRuleFactory;

    /**
     * @var \Magento\Framework\Api\SearchResultsInterface
     */
    protected $searchResultsFactory;
    /**
     * @var array
     */
    private $rules = [];

    /**
     * @param RuleFactory $catalogRuleFactory
     * @param \Magento\Framework\Api\SearchResultsInterface $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        RuleFactory $catalogRuleFactory,
        \Magento\Framework\Api\SearchResultsInterface $searchResultsFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
    ) {

         $this->_catalogRuleFactory = $catalogRuleFactory;
         $this->searchResultsFactory = $searchResultsFactory;
         $this->dataObjectHelper = $dataObjectHelper;
         $this->dataObjectProcessor = $dataObjectProcessor;
      }

    /**
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {

        $searchResults = $this->searchResultsFactory; //->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $catalogRule = $this->_catalogRuleFactory->create();
        $catalogRuleCollection = $catalogRule->getCollection();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $catalogRuleCollection->addFieldToFilter($fields, $conditions);
            }
        }

        $searchResults->setTotalCount($catalogRuleCollection->getSize());
        $catalogRuleCollection->setCurPage($searchCriteria->getCurrentPage());
        $catalogRuleCollection->setPageSize($searchCriteria->getPageSize());

        foreach($catalogRuleCollection as $ruleModel)
        {

            $catalogRuleData = $this->_catalogRuleFactory->create();
            $this->rules[] = $ruleModel->getData();
        }

        $this->searchResultsFactory->setItems($this->rules);
        return $this->searchResultsFactory;

    }
}
