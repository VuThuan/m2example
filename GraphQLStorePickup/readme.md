#  Create New GraphQLStorePickup  In Magento 2


## Goal
- Create New GraphQLStorePickup Using Setup db script.

![](docs/attributeSet.png)


## Step By Step Tutorials

- [app/code/Bdcrops/GraphQLStorePickup/registration.php](registration.php)

    <details><summary>Source</summary>

      ```
      <?php
          \Magento\Framework\Component\ComponentRegistrar::register(
              \Magento\Framework\Component\ComponentRegistrar::MODULE,
              'Bdcrops_GraphQLStorePickup',
              __DIR__
          );
      ```
    </details>


- Create [app/code/Bdcrops/GraphQLStorePickup/etc/module.xml](etc/module.xml)

  <details><summary>Source</summary>

      ```
      <?xml version="1.0"?>
      <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
      <module name="Bdcrops_GraphQLStorePickup" setup_version="1.0.0"/>
      </config>

      ```
  </details>

- [app/code/Bdcrops/GraphQLStorePickup/etc/db_schema.xml](etc/db_schema.xml)


```
php bin/magento setup:db-declaration:generate-whitelist [options]
php bin/magento setup:db-declaration:generate-whitelist --module-name=vendor_module

php bin/magento setup:db-declaration:generate-whitelist --module-name=Bdcrops_GraphQLStorePickup
php bin/magento setup:upgrade --dry-run=1 --keep-generated
php bin/magento setup:upgrade

```


## Ref
- [larsroettig](https://larsroettig.dev/how-to-create-a-graph-ql-endpoint-for-magento-2-3)
