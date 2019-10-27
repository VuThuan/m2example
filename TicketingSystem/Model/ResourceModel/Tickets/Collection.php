<?php
namespace Bdcrops\TicketingSystem\Model\ResourceModel\Tickets;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'bdcrops_ticketingsystem_ticket_collection';
    protected $_eventObject = 'ticket_collection';
    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Bdcrops\TicketingSystem\Model\Tickets', 'Bdcrops\TicketingSystem\Model\ResourceModel\Tickets');
    }
}
