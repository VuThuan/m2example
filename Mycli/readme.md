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

- Mycli/Console/Command/CustomerUserCreateCommand.php
- Mycli/Console/Sayhello.php
- Mycli/Helper/Customer.php
- Mycli/doc/MyCli2.png
- Mycli/doc/MyCliDBData.png
- Mycli/doc/MycliDublicate.png
- Mycli/doc/addDataCli.png
- Mycli/doc/helloCli.png
- Mycli/doc/sayHelloCli.png
- Mycli/etc/di.xml
- Mycli/etc/module.xml
- Mycli/readme.md
- Mycli/registration.php


## Ref
- [blogtreat](http://www.blogtreat.com/create-an-attribute-set-in-magento-2-via-installable-script/)
- [meetanshi](https://meetanshi.com/blog/create-attribute-set-programmatically-in-magento-2/)
- [magenticians](https://magenticians.com/create-magento-2-attribute/)
