<?php
namespace Bdcrops\News\Api;

use Bdcrops\News\Api\Data\NewsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface NewsRepositoryInterface 
{
    public function save(NewsInterface $page);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(NewsInterface $page);

    public function deleteById($id);
}
