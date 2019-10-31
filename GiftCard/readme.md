#  Create New ProductType In Magento 2


## Goal
- Create New ProductType  Using Setup db script.



## Step By Step Tutorials

- [app/code/Bdcrops/GiftCard/registration.php](registration.php)

    <details><summary>Source</summary>
    ```
    <?php
    use Magento\Framework\Component\ComponentRegistrar;
    ComponentRegistrar::register(ComponentRegistrar::MODULE, 'Bdcrops_GiftCard',
        __DIR__
    );
    ```
    </details>


- [GiftCard/etc/module.xml](etc/module.xml)
    ```
    <?xml version="1.0"?>
    <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
        <module name="Bdcrops_GiftCard" setup_version="1.0.0">
            <sequence>
                <module name="Magento_Backend"/>
                <module name="Magento_Catalog"/>
                <module name="Magento_Checkout"/>
                <module name="Magento_Quote"/>
                <module name="Bdcrops_Core"/>
            </sequence>
        </module>
    </config>
    ```
- [GiftCard/etc/product_types.xml](etc/product_types.xml)

    ```
    <?xml version="1.0"?>
    <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Catalog:etc/product_types.xsd">
        <type name="mpgiftcard" label="Gift Card Product" modelInstance="Bdcrops\GiftCard\Model\Product\Type\GiftCard" indexPriority="45" sortOrder="70" isQty="true">
            <priceModel instance="Bdcrops\GiftCard\Model\Product\Price"/>
            <customAttributes>
                <attribute name="refundable" value="true"/>
            </customAttributes>
        </type>
    </config>

    ```
    

## Ref
