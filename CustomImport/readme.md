#  Create CustomImport In Magento 2



## Goal
- Create CustomImport.

![](docs/customImport.jpg)
[csv](docs/bdcrops_custom.csv)

## Step By Step Tutorials

- [app/code/Bdcrops/CustomImport/registration.php](registration.php)

    <details><summary>Source</summary>
    ```
    <?php
        \Magento\Framework\Component\ComponentRegistrar::register(
            \Magento\Framework\Component\ComponentRegistrar::MODULE,
            'Bdcrops_CustomImport',
            __DIR__
        );
    ```
    </details>


- [app/code/Bdcrops/CustomImport/etc/module.xml](etc/module.xml)
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Bdcrops_CustomImport" setup_version="1.0.0"/>
</config>

```
- [CustomImport/etc/db_schema.xml](etc/db_schema.xml)

```
php bin/magento setup:db-declaration:generate-whitelist [options]
php bin/magento setup:db-declaration:generate-whitelist --module-name=vendor_module

php bin/magento setup:db-declaration:generate-whitelist --module-name=Bdcrops_CustomImport
php bin/magento setup:upgrade --dry-run=1 --keep-generated
php bin/magento setup:upgrade

```

## Ref
- [meetanshi](https://www.scommerce-mage.com/blog/magento-2-how-to-import-csv-to-custom-table.html)
