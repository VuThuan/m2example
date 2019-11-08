#  Create New OrderNotes Programmatically In Magento 2


## Goal
- Create New OrderNotes Using Setup db script.

![](docs/attributeSet.png)


## Step By Step Tutorials

- [app/code/Bdcrops/OrderNotes/registration.php](registration.php)

    <details><summary>Source</summary>

      ```
      <?php
          \Magento\Framework\Component\ComponentRegistrar::register(
              \Magento\Framework\Component\ComponentRegistrar::MODULE,
              'Bdcrops_OrderNotes',
              __DIR__
          );
      ```
    </details>


- Create [app/code/Bdcrops/OrderNotes/etc/module.xml](etc/module.xml)

  <details><summary>Source</summary>

      ```
      <?xml version="1.0"?>
      <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
      <module name="Bdcrops_OrderNotes" setup_version="1.0.0"/>
      </config>

      ```
  </details>



## Ref 
