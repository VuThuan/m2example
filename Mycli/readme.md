#  Create New Mycli Programmatically In Magento 2


## Goal
- Create New Mycli Using Setup db script.

![](docs/attributeSet.png)


## Step By Step Tutorials

- [app/code/Bdcrops/Mycli/registration.php](registration.php)

    <details><summary>Source</summary>

      ```
      <?php
          \Magento\Framework\Component\ComponentRegistrar::register(
              \Magento\Framework\Component\ComponentRegistrar::MODULE,
              'Bdcrops_Mycli',
              __DIR__
          );
      ```
    </details>


- Create [app/code/Bdcrops/Mycli/etc/module.xml](etc/module.xml)

  <details><summary>Source</summary>

      ```
      <?xml version="1.0"?>
      <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
      <module name="Bdcrops_Mycli" setup_version="1.0.0"/>
      </config>

      ```
  </details>

- Create [app/code/Bdcrops/Mycli/Setup/InstallData.php](Setup/InstallData.php)

  <details><summary>Source</summary>

      ```
      <?php
      namespace Bdcrops\Mycli\Setup;

      use Magento\Framework\Setup\InstallDataInterface;
      use Magento\Framework\Setup\ModuleContextInterface;
      use Magento\Framework\Setup\ModuleDataSetupInterface;
      use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
      use Magento\Catalog\Setup\CategorySetupFactory;

      class InstallData implements InstallDataInterface {
        private $attributeSetFactory;
        private $categorySetupFactory;
        public function __construct(
            AttributeSetFactory $attributeSetFactory,
            CategorySetupFactory $categorySetupFactory) {
            $this->attributeSetFactory = $attributeSetFactory;
            $this->categorySetupFactory = $categorySetupFactory;
        }

        public function install(
            ModuleDataSetupInterface $setup,
            ModuleContextInterface $context ) {
            $setup->startSetup();
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $attributeSet = $this->attributeSetFactory->create();
            $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
            $data = [
                'attribute_set_name' => 'Bdcrops',
                'entity_type_id' => $entityTypeId,
                'sort_order' => 100,
            ];
            $attributeSet->setData($data);
            $attributeSet->validate();
            $attributeSet->save();
            $attributeSet->initFromSkeleton($attributeSetId)->save();
        }
      }

      ```
  </details>

## Ref
- [blogtreat](http://www.blogtreat.com/create-an-attribute-set-in-magento-2-via-installable-script/)
- [meetanshi](https://meetanshi.com/blog/create-attribute-set-programmatically-in-magento-2/)
- [magenticians](https://magenticians.com/create-magento-2-attribute/)
