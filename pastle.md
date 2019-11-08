
php pestle.phar generate_module Bdcrops HelloPestle 1.0.0
php pestle.phar generate_module Bdcrops CategoryAttributes 1.0.0

pestle.phar generate_route Pulsestorm_HelloPestle frontend hello_pestle
pestle.phar generate_view Pulsestorm_HelloPestle frontend hello_pestle_index_index Main content.phtml

php pestle.phar magento2:generate:theme Bdcrops M2theme


php pestle.phar list


php pestle.phar generate_module Bdcrops JavascriptInitTutorial 1.0.0
php pestle.phar generate_route Bdcrops_JavascriptInitTutorial frontend javascriptinittutorial
php pestle.phar generate_view Bdcrops_JavascriptInitTutorial frontend javascriptinittutorial_index_index Main content.phtml 1column
php bin/magento module:enable Bdcrops_JavascriptInitTutorial
php bin/magento setup:upgrade


php pestle.phar magento2:generate:full-module Bdcrops News
One Word Model Name? (Thing)] News

#!/bin/bash
php pestle.phar magento2:generate:module Bdcrops News 0.0.1
php pestle.phar magento2:generate:crud-model Bdcrops_News News
php pestle.phar magento2:generate:acl Bdcrops_News Bdcrops_News::newss
php pestle.phar magento2:generate:menu Bdcrops_News "" Bdcrops_News::newss Bdcrops_News::newss "News newss" bdcrops_news_newss/index/index 10
php pestle.phar magento2:generate:menu Bdcrops_News Bdcrops_News::newss Bdcrops_News::newss_list Bdcrops_News::newss "News Objects" bdcrops_news_newss/index/index 10
php pestle.phar magento2:generate:route Bdcrops_News adminhtml bdcrops_news_newss Index Index
php pestle.phar magento2:generate:view Bdcrops_News adminhtml bdcrops_news_newss_index_index Main content.phtml 1column
php pestle.phar magento2:generate:ui:grid Bdcrops_News bdcrops_news_newss 'Bdcrops\News\Model\ResourceModel\News\Collection' news_id
php pestle.phar magento2:generate:ui:add-column-text app/code/Bdcrops/News/view/adminhtml/ui_component/bdcrops_news_newss.xml title "Title"
php pestle.phar magento2:generate:ui:form Bdcrops_News 'Bdcrops\News\Model\News' Bdcrops_News::newss
php pestle.phar magento2:generate:ui:add_to_layout app/code/Bdcrops/News/view/adminhtml/layout/bdcrops_news_newss_index_index.xml content bdcrops_news_newss
php pestle.phar magento2:generate:acl:change_title app/code/Bdcrops/News/etc/acl.xml Bdcrops_News::newss "Manage newss"
php pestle.phar magento2:generate:controller_edit_acl app/code/Bdcrops/News/Controller/Adminhtml/Index/Index.php Bdcrops_News::newss
php pestle.phar magento2:generate:remove-named-node app/code/Bdcrops/News/view/adminhtml/layout/bdcrops_news_newss_index_index.xml block bdcrops_news_block_main

php bin/magento module:enable Bdcrops_News




https://alanstorm.com/magento2_pestle_code_generation/
https://pestle.readthedocs.io/en/latest/magento2-generate-full-module/



git add .
git commit -m "doc commit"
git push -u origin master


git config --global user.name "Abdul Matin"
git config --global user.email "matinict@gmail.com"
git init
git remote add origin https://gitlab.com/matinict/m2p.git
git add .
git commit -m "Initial commit"
git push -u origin master


#test

https://mirasvit.com/docs/module-customer-segment/current/setup/installation
https://mirasvit.com/docs/module-rewards/current/setup/installation
https://amasty.com/admin-actions-log-for-magento-2.html
https://amasty.com/abandoned-cart-email-for-magento-2.html

https://www.mageplaza.com/magento-2-one-step-checkout-extension/
https://www.mageplaza.com/magento-2-search-extension/
https://www.mageplaza.com/magento-2-gift-card-extension/
https://www.mageplaza.com/magento-2-google-tag-manager/




***

Usage: pestle command_name [options] [arguments]
Available commands:
Alanstormdotcom
  alanstormdotcom:rsync                      One Line Description

Codecept
  codecept:convert-selenium-id-for-codecept  Converts a selenium IDE html test for conception

Faker
  faker:names                                Creates some Fake Name

Gs
  gs:combine                                 One Line Description

Magento1 Convert
  magento1:convert:generate-maps             ALPHA: Wrapper for Magento's code-migration tools
  magento1:convert:magentoinc                ALPHA: Wrapper for Magento Inc.'s code-migration tool
  magento1:convert:unirgy                    ALPHA: Wrapper for Unirgy Magento Module Conversion

Magento2
  magento2:base-dir                          Output the base magento2 directory
  magento2:check-templates                   Checks for incorrectly named template folder
  magento2:class-from-path                   Turns a Magento file path into a PHP class
  magento2:class-list                        Get a list of all of magento2's extensible classes
  magento2:convert-class                     ALPHA: Partially converts Magento 1 class to Magento 2
  magento2:convert-observers-xml             ALPHA: Partially converts Magento 1 config.xml to Magento 2
  magento2:convert-system-xml                ALPHA: Partially Converts Magento 1 system.xml into Magento 2 system.xml
  magento2:extract-mage2-system-xml-paths    Generates Mage2 config.xml
  magento2:fix-direct-om                     ALPHA: Fixes direct use of PHP Object Manager
  magento2:fix-permissions-modphp            ALPHA: "Fixes" permissions for development boxes
  magento2:path-from-class                   Turns a PHP class into a Magento 2 path
  magento2:read-rest-schema                  BETA: Magento command, reads the rest schema on a Magento system

Magento2 Code-migration
  magento2:code-migration:rename             ALPHA: Rename .converted files

Magento2 Generate
  magento2:generate:acl                      Generates a Magento 2 acl.xml file.
  magento2:generate:acl:change-title         Changes the title of a specific ACL rule in a Magento 2 acl.xml file
  magento2:generate:class-child              Generates a child class, pulling in __ constructor for easier di
  magento2:generate:command                  Generates bin/magento command files
  magento2:generate:config-helper            Generates a help class for reading Magento's configuration
  magento2:generate:controller-edit-acl      Edits the const ADMIN_RESOURCE value of an admin controller
  magento2:generate:crud-model               Generates a Magento 2 CRUD/AbstractModel class and support files
  magento2:generate:di                       Injects a dependency into a class constructor
  magento2:generate:full-module              Creates shell script with all pestle commands needed for full module output
  magento2:generate:install                  BETA: Generates commands to install Magento via composer
  magento2:generate:menu                     Generates configuration for Magento Adminhtml menu.xml files
  magento2:generate:module                   Generates new module XML, adds to file system
  magento2:generate:observer                 Generates Magento 2 Observer
  magento2:generate:plugin-xml               Generates plugin XML
  magento2:generate:preference               Generates a Magento 2.1 ui grid listing and support classes.
  magento2:generate:psr-log-level            For conversion of Zend Log Level into PSR Log Level
  magento2:generate:registration             Generates registration.php
  magento2:generate:remove-named-node        Removes a named node from a generic XML configuration file
  magento2:generate:route                    Creates a Route XML
  magento2:generate:schema-add-column        Genreated a Magento 2 addColumn DDL definition and inserts into file
  magento2:generate:schema-upgrade           BETA: Generates a migration-based UpgradeSchema and UpgradeData classes
  magento2:generate:service-contract         ALPHA: Service Contract Generator
  magento2:generate:theme                    Generates Theme Configuration
  magento2:generate:ui:add-column-text       Adds a simple text column to a UI Component Grid
  magento2:generate:ui:add-form-field        Adds a Form Field
  magento2:generate:ui:add-form-fieldset     Add a Fieldset to a Form
  magento2:generate:ui:add-to-layout         Adds a <uiComponent/> node to a named node in a layout update XML file
  magento2:generate:ui:form                  Generates a Magento 2 UI Component form configuration and PHP boilerplate
  magento2:generate:ui:grid                  Generates a Magento 2.1 ui grid listing and support classes.
  magento2:generate:view                     Generates view files (layout handle, phtml, Block, etc.)

Magento2 Scan
  magento2:scan:acl-used                     Scans modules for ACL rule ids, makes sure they're all used/defined
  magento2:scan:class-and-namespace          BETA: Scans a Magento 2 module for misnamed PHP classes
  magento2:scan:htaccess                     ALPHA: Checks for missing Magento 2 HTACCESS files from a hard coded list
  magento2:scan:registration                 Scans Magento 2 directories for missing registration.php files

Magento2 Search
  magento2:search:search-controllers         Searches controllers

Mysql
  mysql:key-check                            Looks for Invalid Keys in a MySQL Database

Nexmo
  nexmo:send-text                            Sends a text message
  nexmo:store-credentials                    Stores Nexmo API in temp file
  nexmo:verify-request                       Sends initial request to verify user's phone number
  nexmo:verify-sendcode                      Nexmo Verify API: Second Step

Parsing
  parsing:citicard                           BETA: Parses Citicard's CSV files into yaml
  parsing:csv-to-iif                         BETA: Converts a CSV file to .iif

Pestle
  pestle:baz-bar                             Another Hello World we can probably discard
  pestle:build-command-list                  Builds the command list
  pestle:clear-cache                         BETA: Clears the pestle cache
  pestle:dev-import                          Another Hello World we can probably discard
  pestle:dev-namespace                       BETA: Used to move old pestle files to module.php -- still needed?
  pestle:export-as-symfony                   Exports a Pestle Module as a Symfony Console Command
  pestle:export-module                       ALPHA: Seems to be a start at exporting a pestle module as functions.
  pestle:foo-bar                             ALPHA: Another Hello World we can probably discard
  pestle:generate-command                    Generates pestle command boiler plate
  pestle:hello-argument                      A demo of pestle argument and option configuration/parsing
  pestle:pestle-run-file                     ALPHA: Stub for running a single PHP file in a pestle context

Php
  php:extract-session                        ALPHA: Extracts data from a saved PHP session file
  php:format-php                             ALPHA: Experiments with a PHP formatter.
  php:test-namespace-integrity               ALPHA: Tests the "namespace integrity?  Not sure what this is anymore.

Postscript
  postscript:check                           Outputs the PostScript code needed to print a check

Pulsestorm
  pulsestorm:build-book                      BETA: Command for building No Frills Magento 2 Layout
  pulsestorm:md-to-say                       Converts a markdown files to an aiff
  pulsestorm:monty-hall-problem              Runs Simulation of "Monty Hall Problem"
  pulsestorm:orphan-content                  BETA: Used to scan my old pre-Wordpress archives for missing pages.
  pulsestorm:pandoc-md                       BETA: Uses pandoc to converts a markdown file to pdf, epub, epub3, html, txt
  pulsestorm:solo-noble                      One Line Description

Uncategorized
  hello-world                                A Hello World command.  Hello World!
  help                                       Alias for list
  list-commands                              Lists help
  selfupdate                                 Updates the pestle.phar file to the latest version
  test-output                                A test command for the output function that should probably be pruned
  testbed                                    Test Command
  version                                    Displays Pestle Version
