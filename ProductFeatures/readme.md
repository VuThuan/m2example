#  Create New ProductFeatures Programmatically In Magento 2


## Goal
- Create ProductFeatures Using  extension_attributes

![](docs/attributeSet.png)

## Step By Step Tutorials

- [app/code/Bdcrops/ProductFeatures/registration.php](registration.php)

    <details><summary>Source</summary>
    ```
    <?php
        \Magento\Framework\Component\ComponentRegistrar::register(
            \Magento\Framework\Component\ComponentRegistrar::MODULE,
            'Bdcrops_ProductFeatures',
            __DIR__
        );
    ```
    </details>


- [app/code/Bdcrops/ProductFeatures/etc/module.xml](etc/module.xml)
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Bdcrops_ProductFeatures" setup_version="1.0.0"/>
</config>

```
- [app/code/Bdcrops/ProductFeatures/Setup/InstallData.php](Setup/InstallData.php)
```
<?php
namespace Bdcrops\ProductFeatures\Setup;

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
- Define our configuration in extension_attributes.xml and di.xml
The first step is to register our extension attribute with Magento 2. We do this like with extension_attributes.xml which lives within the etc directory of your module.

etc/extension_attributes.xml
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Api/etc/extension_attributes.xsd">
    <extension_attributes for="Magento\Catalog\Api\Data\ProductInterface">
        <attribute code="features" type="string" />
    </extension_attributes>
</config>
```
Note: <extension_attributes for=“CLASS”>? This tells Magento 2 which class we are defining an extension attribute for. As we’re doing this for a product, we’ll need to use the Magento\Catalog\Api\Data\ProductInterface interface.

The next line <attribute code=“” type=“” /> defines our attributes. We give them a name, and a type. The type in this case can reference either a PHP type or a class type.

Next up is our di.xml again, living in etc directory. This is so we can register a plugin to set data to our extension attribute (I’ll cover this more in the next step)

## Ref
- [ashsmith](https://www.ashsmith.io/magento2/using-extension-attributes-with-products/)
