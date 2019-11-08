## Add Customer Attribute Programmatically

## Goal

- Add Customer Attribute Using DB Script

![CustomerAttribute](docs/CustomerAttribute.png)

## Step By Step Tutorials


- [app/code/Bdcrops/CustomerAttribute/registration.php](registration.php)

    <details><summary>Source</summary>

      ```
      <?php
          \Magento\Framework\Component\ComponentRegistrar::register(
              \Magento\Framework\Component\ComponentRegistrar::MODULE,
              'Bdcrops_CustomerAttribute',
              __DIR__
          );
      ```
    </details>


- Create [app/code/Bdcrops/CustomerAttribute/etc/module.xml](etc/module.xml)

  <details><summary>Source</summary>

      ```
      <?xml version="1.0"?>
      <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
      <module name="Bdcrops_CustomerAttribute" setup_version="1.0.0"/>
      </config>

      ```
  </details>

- [app/code/Bdcrops/CustomerAttribute/Setup/InstallData.php](Setup/InstallData.php)

	<details><summary>Source</summary>

			```
			<?php
			namespace Bdcrops\CustomerAttribute\Setup;

			use Magento\Eav\Setup\EavSetup;
			use Magento\Eav\Setup\EavSetupFactory;
			use Magento\Framework\Setup\InstallDataInterface;
			use Magento\Framework\Setup\ModuleContextInterface;
			use Magento\Framework\Setup\ModuleDataSetupInterface;
			use Magento\Eav\Model\Config;
			use Magento\Customer\Model\Customer;

			class InstallData implements InstallDataInterface{
			private $eavSetupFactory;

			public function __construct(EavSetupFactory $eavSetupFactory, Config $eavConfig){
				$this->eavSetupFactory = $eavSetupFactory;
				$this->eavConfig       = $eavConfig;
			}

			public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
			{
				$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
				$eavSetup->addAttribute(
					\Magento\Customer\Model\Customer::ENTITY,
					'nid',['type'         => 'varchar',
						'label'        => 'NID',
						'input'        => 'text', 'required'     => false,
						'visible'      => true, 'user_defined' => true,
						'position'     => 999, 'system'       => 0,]
				);
				$sampleAttribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'nid');
				// more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
				$sampleAttribute->setData('used_in_forms',['adminhtml_customer']);
				$sampleAttribute->save();
			}
			}

			```
	</details>


## Ref
[mageplaza](https://www.mageplaza.com/magento-2-module-development/magento-2-add-customer-attribute-programmatically.html)
