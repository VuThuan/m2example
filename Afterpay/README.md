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



    

### ref

https://blog.scandiweb.com/article/magento-2-custom-payment-gateway-afterpay-h84hrhhoxna
