<?php
namespace Bdcrops\News\Model\ResourceModel;
class News extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('bdcrops_news_news','news_id');
    }
}
