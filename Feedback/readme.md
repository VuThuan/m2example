#  Create New Feedback Programmatically In Magento 2


## Goal
- Create New Feedback Using Setup db script.

![](docs/attributeSet.png)


## Step By Step Tutorials

- [app/code/Bdcrops/Feedback/registration.php](registration.php)

    <details><summary>Source</summary>

      ```
      <?php
          \Magento\Framework\Component\ComponentRegistrar::register(
              \Magento\Framework\Component\ComponentRegistrar::MODULE,
              'Bdcrops_Feedback',
              __DIR__
          );
      ```
    </details>


- Create [app/code/Bdcrops/Feedback/etc/module.xml](etc/module.xml)

  <details><summary>Source</summary>

      ```
      <?xml version="1.0"?>
      <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
      <module name="Bdcrops_Feedback" setup_version="1.0.0"/>
      </config>

      ```
  </details>

- Feedback/Block/Customer/Feedback.php
- Feedback/Controller/Customer/Index.php
- Feedback/Controller/Customer/SubmitReview.php
- Feedback/Model/Feedback.php
- Feedback/Model/ResourceModel/Feedback.php
- Feedback/Model/ResourceModel/Feedback/Collection.php
- Feedback/Setup/InstallSchema.php
- Feedback/etc/di.xml
- Feedback/etc/frontend/routes.xml
- Feedback/etc/module.xml
- Feedback/readme.md
- Feedback/registration.php
- Feedback/view/frontend/layout/customer_account.xml
- Feedback/view/frontend/layout/customer_feedback_customer_index.xml
- Feedback/view/frontend/templates/customer_feedback.phtml
- Feedback/view/frontend/web/css/customer-feedback.css
- Feedback/view/frontend/web/js/view/customer-feedback.js
- Feedback/view/frontend/web/template/customer-feedback.html


## Ref
- [blogtreat](http://www.blogtreat.com/create-an-attribute-set-in-magento-2-via-installable-script/)
- [meetanshi](https://meetanshi.com/blog/create-attribute-set-programmatically-in-magento-2/)
- [magenticians](https://magenticians.com/create-magento-2-attribute/)
