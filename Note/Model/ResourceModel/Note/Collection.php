<?php


namespace Bdcrops\Note\Model\ResourceModel\Note;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Bdcrops\Note\Model\Note',
            'Bdcrops\Note\Model\ResourceModel\Note'
        );
    }
}
