# Extended Orders Grid In Magento 2

We need to add a column ― with some regional code of a customer who completed a purchase ― to the orders grid. Additionally, a store administrator must have a possibility to filter orders by this newly added column.

![](docs/rcode01.png)
![](docs/rvalue01.png)


## Goal
- Extended Orders Grid By Ui & Plugins

![](docs/attributeSet.png)


## Step By Step Tutorials

- [app/code/Bdcrops/ExtendedOrdersGrid/registration.php](registration.php)

  <details><summary>Source</summary>

      ```
      <?php
          \Magento\Framework\Component\ComponentRegistrar::register(
              \Magento\Framework\Component\ComponentRegistrar::MODULE,
              'Bdcrops_ExtendedOrdersGrid',
              __DIR__
          );
      ```
  </details>


- [ExtendedOrdersGrid/etc/module.xml](etc/module.xml)

  <details><summary>Source</summary>

    ```
    <?xml version="1.0"?>
    <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
        <module name="Bdcrops_ExtendedOrdersGrid" setup_version="1.0.0"/>
    </config>

    ```
  </details>
- [etc/adminhtml/di.xml](etc/adminhtml/di.xml)

  <details><summary>Source</summary>

    ```
    <?xml version="1.0"?>
    <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
        <!-- Plugins -->
        <!-- Adds additional data to the orders grid collection -->
        <type name="Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory">
            <plugin name="bdcrops_extended_orders_grid_add_data_to_orders_grid"
                    type="Bdcrops\ExtendedOrdersGrid\Plugin\AddDataToOrdersGrid"
                    sortOrder="10" disabled="false"/>
        </type>
    </config>

    ```
  </details>
- [view/adminhtml/ui_component/sales_order_grid.xml](view/adminhtml/ui_component/sales_order_grid.xml)

  <details><summary>Source</summary>

    ```
    <?xml version="1.0" encoding="UTF-8"?>
    <listing xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Ui:etc/ui_configuration.xsd">
        <columns name="sales_order_columns">
            <column name="code">
                <argument name="data" xsi:type="array">
                    <item name="config" xsi:type="array">
                        <item name="component" xsi:type="string">Magento_Ui/js/grid/columns/column</item>
                        <item name="label" xsi:type="string" translate="true">Region Code</item>
                        <item name="sortOrder" xsi:type="number">60</item>
                        <item name="align" xsi:type="string">left</item>
                        <item name="dataType" xsi:type="string">text</item>
                        <item name="visible" xsi:type="boolean">true</item>
                        <item name="filter" xsi:type="string">text</item>
                    </item>
                </argument>
            </column>
        </columns>
    </listing>
    ```
  </details>

- [Plugin/AddDataToOrdersGrid.php](Plugin/AddDataToOrdersGrid.php)

  <details><summary>Source</summary>

      ```
      <?php
      namespace Bdcrops\ExtendedOrdersGrid\Plugin;
      /**
       * Class AddDataToOrdersGrid
       */
      class AddDataToOrdersGrid {
          /**
           * @var \Psr\Log\LoggerInterface
           */
          private $logger;

          /**
           * AddDataToOrdersGrid constructor.
           *
           * @param \Psr\Log\LoggerInterface $customLogger
           * @param array $data
           */
          public function __construct(
              \Psr\Log\LoggerInterface $customLogger,
              array $data = [] ) {
              $this->logger   = $customLogger;
          }

          /**
           * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject
           * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
           * @param $requestName
           * @return mixed
           */
          public function afterGetReport($subject, $collection, $requestName) {
              if ($requestName !== 'sales_order_grid_data_source') {
                  return $collection;
              }

              if ($collection->getMainTable() === $collection->getResource()->getTable('sales_order_grid')) {
                  try {
                      $orderAddressTableName = $collection->getResource()->getTable('sales_order_address');
                      $directoryCountryRegionTableName = $collection->getResource()->getTable('directory_country_region');
                      $collection->getSelect()->joinLeft(
                          ['soa' => $orderAddressTableName],
                          'soa.parent_id = main_table.entity_id AND soa.address_type = \'shipping\'',
                          null
                      );
                      $collection->getSelect()->joinLeft(
                          ['dcrt' => $directoryCountryRegionTableName],
                          'soa.region_id = dcrt.region_id',
                          ['code']
                      );
                  } catch (\Zend_Db_Select_Exception $selectException) {
                      // Do nothing in that case
                      $this->logger->log(100, $selectException);
                  }
              }

              return $collection;
          }
        }
     ```
  </details>

## Ref
- [mageworx](https://www.mageworx.com/blog/how-to-add-column-with-filter-to-magento-2-orders-grid/)
- [github](https://github.com/mageworx/articles-extended-orders-grid)
