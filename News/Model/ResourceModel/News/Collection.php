<?php
namespace Bdcrops\News\Model\ResourceModel\News;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Bdcrops\News\Model\News','Bdcrops\News\Model\ResourceModel\News');
    }
}
