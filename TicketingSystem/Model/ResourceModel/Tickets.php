<?php
namespace Bdcrops\TicketingSystem\Model\ResourceModel;
class Tickets extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('bdcrops_ticketing_system', 'id');
        //$this->_init('table name', 'primary key column name');
    }
}
