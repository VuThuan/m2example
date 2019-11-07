<?php
namespace Bdcrops\News\Model;
class News extends \Magento\Framework\Model\AbstractModel implements \Bdcrops\News\Api\Data\NewsInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'bdcrops_news_news';

    protected function _construct()
    {
        $this->_init('Bdcrops\News\Model\ResourceModel\News');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
