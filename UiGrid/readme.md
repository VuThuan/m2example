#  Create UI Grid Component on the Front End In Magento 2


## Goal
- Create  



## Step By Step Tutorials

- [app/code/Bdcrops/UiGrid/registration.php](registration.php)

    <details><summary>Source</summary>
    ```
    <?php
        \Magento\Framework\Component\ComponentRegistrar::register(
            \Magento\Framework\Component\ComponentRegistrar::MODULE,
            'Bdcrops_UiGrid',
            __DIR__
        );
    ```
    </details>


- [app/code/Bdcrops/UiGrid/etc/module.xml](etc/module.xml)
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Bdcrops_UiGrid" setup_version="1.0.0"/>
</config>

```


## Ref
- [belvg](https://belvg.com/blog/ui-grid-component-on-the-front-end-in-magento-2.html)
- [ ](https://github.com/belvg-public/ui-grid)
