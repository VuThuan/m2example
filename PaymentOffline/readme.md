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

- [Model/Config/Source/Order/Status/Pendingpayment.php](Model/Config/Source/Order/Status/Pendingpayment.php)
- [Model/PaymentOffline.php](Model/PaymentOffline.php)
- [etc/adminhtml/system.xml](etc/adminhtml/system.xml)
- [etc/config.xml](etc/config.xml)
- [etc/payment.xml](etc/payment.xml)
- [view/frontend/layout/checkout_index_index.xml](view/frontend/layout/checkout_index_index.xml)
- [view/frontend/web/js/view/payment/method-renderer/paymentoffline-method.js](view/frontend/web/js/view/payment/method-renderer/paymentoffline-method.js)
- [view/frontend/web/js/view/payment/paymentoffline.js](view/frontend/web/js/view/payment/paymentoffline.js)
- [view/frontend/web/template/payment/paymentoffline.html](view/frontend/web/template/payment/paymentoffline.html)


## Ref

- [magestore](https://www.magestore.com/magento-2-tutorial/2361-2/)
