# Magento2_Afterpay
Afterpay Payment Method for Magento 2




## Goal
- Create New Afterpay Using Setup db script.


## Step By Step Tutorials

- [app/code/Bdcrops/Afterpay/registration.php](registration.php)

    <details><summary>Source</summary>
    ```
    <?php
        \Magento\Framework\Component\ComponentRegistrar::register(
            \Magento\Framework\Component\ComponentRegistrar::MODULE,
            'Bdcrops_Afterpay',
            __DIR__
        );
    ```
    </details>

- Block/Adminhtml/System/Config/Button/Update.php
- Block/Adminhtml/System/Config/Form/Field/Disable.php
- Block/Adminhtml/System/Config/Form/Field/Label.php
- Block/Cart/Button.php
- Block/Catalog/Installments.php
- Block/Config.php
- Block/Info.php
- Controller/Payment/Process.php
- Controller/Payment/Response.php
- Helper/Data.php
- Model/Adapter/Afterpay/AfterpayClient.php
- Model/Adapter/Afterpay/AfterpayResponse.php
- Model/Adapter/Afterpay/Call.php
- Model/Adapter/AfterpayPayment.php
- Model/Adapter/AfterpayTotalLimit.php
- Model/Adapter/ApiMode.php
- Model/Adapter/V1/AfterpayOrderDirectCapture.php
- Model/Adapter/V1/AfterpayOrderTokenCheck.php
- Model/Adapter/V1/AfterpayOrderTokenV1.php
- Model/Config/Payovertime.php
- Model/Config/Save/Plugin.php
- Model/Config/Source/Categorylist.php
- Model/ConfigProvider.php
- Model/Cron/Limit.php
- Model/GuestPaymentInformationManagement/Plugin.php
- Model/Logger/Handler.php
- Model/Logger/Logger.php
- Model/PaymentInformationManagement/Plugin.php
- Model/Payovertime.php
- Model/Response.php
- Model/Source/ApiMode.php
- Model/Source/PaymentAction.php
- Model/Source/PaymentDisplay.php
- Model/Status.php
- Model/Token.php
- README.md
- Test/Unit/Block/Adminhtml/System/Config/Button/UpdateTest.php
- Test/Unit/Model/Adapter/AfterpayOrderTokenTest.php
- assets.ini
- composer.json
- etc/adminhtml/di.xml
- etc/adminhtml/routes.xml
- etc/adminhtml/system.xml
- etc/config.xml
- etc/crontab.xml
- etc/di.xml
- etc/frontend/di.xml
- etc/frontend/routes.xml
- etc/module.xml
- etc/payment.xml
- phpunit-afterpay.xml
- registration.php
- view/adminhtml/layout/adminhtml_system_config_edit.xml
- view/adminhtml/templates/system/config/button/update.phtml
- view/adminhtml/web/images/afterpay_logo.png
- view/adminhtml/web/styles.css
- view/frontend/layout/catalog_product_view.xml
- view/frontend/layout/catalog_product_view_type_bundle.xml
- view/frontend/layout/catalog_product_view_type_giftcard.xml
- view/frontend/layout/checkout_cart_index.xml
- view/frontend/layout/checkout_index_index.xml
- view/frontend/requirejs-config.js
- view/frontend/templates/afterpay/cart.phtml
- view/frontend/templates/afterpay/modal.phtml
- view/frontend/templates/afterpay/product.phtml
- view/frontend/templates/config.phtml
- view/frontend/web/css/afterpay.css
- view/frontend/web/images/afterpay_logo.png
- view/frontend/web/images/afterpay_logo_white.png
- view/frontend/web/images/circle_1@2x.png
- view/frontend/web/images/circle_2@2x.png
- view/frontend/web/images/circle_3@2x.png
- view/frontend/web/images/circle_4@2x.png
- view/frontend/web/js/view/payment/afterpay-payments.js
- view/frontend/web/js/view/payment/method-renderer/afterpaypayovertime.js
- view/frontend/web/js/view/product/afterpay-products.js
- view/frontend/web/template/payment/afterpaypayovertime.html




### ref

- [blog.scandiweb](https://blog.scandiweb.com/article/magento-2-custom-payment-gateway-afterpay-h84hrhhoxna)
