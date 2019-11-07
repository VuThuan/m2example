#  Create a new offline payment method In Magento 2


## Goal
- Create New AttributesSet Using Setup db script.

![](docs/attributeSet.png)

## Step By Step Tutorials

- [app/code/Bdcrops/PaymentOffline/registration.php](registration.php)

    <details><summary>Source</summary>
    ```
    <?php
        \Magento\Framework\Component\ComponentRegistrar::register(
            \Magento\Framework\Component\ComponentRegistrar::MODULE,
            'Bdcrops_PaymentOffline',
            __DIR__
        );
    ```
    </details>


- [app/code/Bdcrops/PaymentOffline/etc/module.xml](etc/module.xml)
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Bdcrops_PaymentOffline" setup_version="1.0.0"/>
</config>

```



## Ref

- [magestore](https://www.magestore.com/magento-2-tutorial/2361-2/)
