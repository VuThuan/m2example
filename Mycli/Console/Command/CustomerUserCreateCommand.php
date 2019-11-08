<?php
namespace Bdcrops\Mycli\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Bdcrops\Mycli\Helper\Customer;

class CustomerUserCreateCommand extends Command
{
    protected $customerHelper;

    public function __construct(Customer $customerHelper)
    {
        $this->customerHelper = $customerHelper;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('bdcrops:user:create')
            ->setDescription('Create new customer')
            ->setDefinition($this->getOptionsList());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Creating new user...</info>');
        $this->customerHelper->setData($input);
        $this->customerHelper->execute();

        $output->writeln('');
        $output->writeln('<info>User created with the following data:</info>');
        $output->writeln('<comment>Customer ID: ' . $this->customerHelper->getCustomerId());
        $output->writeln('<comment>Customer Website ID ' . $input->getOption(Customer::KEY_WEBSITE));
        $output->writeln('<comment>Customer First Name: ' . $input->getOption(Customer::KEY_FIRSTNAME));
        $output->writeln('<comment>Customer Last Name: ' . $input->getOption(Customer::KEY_LASTNAME));
        $output->writeln('');
        $output->writeln('<comment>Customer Email: ' . $input->getOption(Customer::KEY_EMAIL));
        $output->writeln('<comment>Customer Password: ' . $input->getOption(Customer::KEY_PASSWORD));
    }

    protected function getOptionsList(){
        return [
            new InputOption(Customer::KEY_FIRSTNAME, null, InputOption::VALUE_REQUIRED, '(Required) Customer first name'),
            new InputOption(Customer::KEY_LASTNAME, null, InputOption::VALUE_REQUIRED, '(Required) Customer last name'),
            new InputOption(Customer::KEY_EMAIL, null, InputOption::VALUE_REQUIRED, '(Required) Customer email'),
            new InputOption(Customer::KEY_PASSWORD, null, InputOption::VALUE_REQUIRED, '(Required) Customer password'),
            new InputOption(Customer::KEY_WEBSITE, null, InputOption::VALUE_REQUIRED, '(Required) Website ID'),
            new InputOption(Customer::KEY_SENDEMAIL, 0, InputOption::VALUE_OPTIONAL, '(1/0) Send email? (default 0)')
        ];
    }
}
