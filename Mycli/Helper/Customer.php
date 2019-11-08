<?php
namespace Bdcrops\Mycli\Helper;

use \Magento\Framework\App\Helper\Context;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\App\State;
use \Magento\Customer\Model\CustomerFactory;
use \Symfony\Component\Console\Input\Input;

class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    const KEY_EMAIL = 'customer-email';
    const KEY_FIRSTNAME = 'customer-firstname';
    const KEY_LASTNAME = 'customer-lastname';
    const KEY_PASSWORD = 'customer-password';
    const KEY_WEBSITE = 'website';
    const KEY_SENDEMAIL = 'send-email';

    protected $storeManager;
    protected $state;
    protected $customerFactory;
    protected $data;
    protected $customerId;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        State $state,
        CustomerFactory $customerFactory
    ) {
        $this->storeManager = $storeManager;
        $this->state = $state;
        $this->customerFactory = $customerFactory;

        parent::__construct($context);
    }

    public function setData(Input $input)
    {
        $this->data = $input;
        return $this;
    }

    public function execute()
    {
        $this->state->setAreaCode('frontend');

        $customer = $this->customerFactory->create();
        $customer
            ->setWebsiteId($this->data->getOption(self::KEY_WEBSITE))
            ->setEmail($this->data->getOption(self::KEY_EMAIL))
            ->setFirstname($this->data->getOption(self::KEY_FIRSTNAME))
            ->setLastname($this->data->getOption(self::KEY_LASTNAME))
            ->setPassword($this->data->getOption(self::KEY_PASSWORD));
        $customer->save();

        $this->customerId = $customer->getId();

        if($this->data->getOption(self::KEY_SENDEMAIL)) {
            $customer->sendNewAccountEmail();
        }
    }

    public function getCustomerId()
    {
        return (int)$this->customerId;
    }
}
