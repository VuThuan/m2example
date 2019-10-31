<?php
namespace Bdcrops\Contacts\Model\ResourceModel\Contact;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'bdcrops_contacts_contact_id';

    protected function _construct()
    {
        $this->_init('Bdcrops\Contacts\Model\Contact','Bdcrops\Contacts\Model\ResourceModel\Contact');
    }
}
