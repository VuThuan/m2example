#  Create New Minventory Programmatically In Magento 2


## Goal
- Create New Minventory Using Setup db script.

![](docs/attributeSet.png)


## Step By Step Tutorials

- [app/code/Bdcrops/Minventory/registration.php](registration.php)

    <details><summary>Source</summary>

      ```
      <?php
          \Magento\Framework\Component\ComponentRegistrar::register(
              \Magento\Framework\Component\ComponentRegistrar::MODULE,
              'Bdcrops_Minventory',
              __DIR__
          );
      ```
    </details>


- Create [app/code/Bdcrops/Minventory/etc/module.xml](etc/module.xml)

  <details><summary>Source</summary>

      ```
      <?xml version="1.0"?>
      <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
      <module name="Bdcrops_Minventory" setup_version="1.0.0"/>
      </config>

      ```
  </details>

- Minventory/Block/Adminhtml/Product/Edit/Button/Back.php
- Minventory/Block/Adminhtml/Product/Edit/Button/Save.php
- Minventory/Controller/Adminhtml/Product.php
- Minventory/Controller/Adminhtml/Product/Index.php
- Minventory/Controller/Adminhtml/Product/MassResupply.php
- Minventory/Controller/Adminhtml/Product/Resupply.php
- Minventory/Model/Resupply.php
- Minventory/Ui/Component/Listing/Columns/Resupply.php
- Minventory/Ui/DataProvider/Product/Form/ProductDataProvider.php
- Minventory/Ui/DataProvider/Product/ProductDataProvider.php
- Minventory/docs/UiComponent.png
- Minventory/docs/aclMenu.png
- Minventory/docs/massResuply.png
- Minventory/docs/menuDash.png
- Minventory/docs/minventoryList.png
- Minventory/docs/resupplyStock.png
- Minventory/docs/resupplyaddAfter.png
- Minventory/etc/acl.xml
- Minventory/etc/adminhtml/menu.xml
- Minventory/etc/adminhtml/routes.xml
- Minventory/etc/module.xml
- Minventory/readme.md
- Minventory/registration.php
- Minventory/view/adminhtml/layout/minventory_product_index.xml
- Minventory/view/adminhtml/layout/minventory_product_resupply.xml
- Minventory/view/adminhtml/ui_component/minventory_listing.xml
- Minventory/view/adminhtml/ui_component/minventory_resupply_form.xml


## Ref
- [blogtreat](http://www.blogtreat.com/create-an-attribute-set-in-magento-2-via-installable-script/)
- [meetanshi](https://meetanshi.com/blog/create-attribute-set-programmatically-in-magento-2/)
- [magenticians](https://magenticians.com/create-magento-2-attribute/)
