# Bdcrops_CatalogWidget

This module is used as a wrapper for all BDCrops Magento 2 extensions.

pestle.phar generate_module Bdcrops ProductAttrbutes 1.0.0

## How to install & upgrade Bdcrops_CatalogWidget


### 1.1 Copy and paste

If you don't want to install via composer, you can use this way.

- Download [the latest version here](https://github.com/bdcrops/module-catalogwidget/archive/master.zip)
- Extract `master.zip` file to `app/code/BDC/CatalogWidget` ; You should create a folder path `app/code/BDC/CatalogWidget` if not exist.
- Go to Magento root folder and run upgrade command line to install `Bdcrops_CatalogWidget`:

```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```


### 1.2 Install via composer

We recommend you to install Bdcrops_CatalogWidget module via composer. It is easy to install, update and maintaince. Run the following command in Magento 2 root folder.
Run
```
composer config repositories.module-catalogwidget git
https://github.com/bdcrops/module-catalogwidget.git

composer require bdcrops/module-catalogwidget:~1.0.0
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```




[addwidget](docs/addwidget.png)

## Ref

https://inchoo.net/magento-2/magento-2-custom-widget/
