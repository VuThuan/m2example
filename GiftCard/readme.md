#  Create New ProductType In Magento 2


## Goal
- Create New ProductType  Using Setup db script.



## Step By Step Tutorials

- [app/code/Bdcrops/GiftCard/registration.php](registration.php)

  <details><summary>Source</summary>

      ```
      <?php
      use Magento\Framework\Component\ComponentRegistrar;
      ComponentRegistrar::register(ComponentRegistrar::MODULE, 'Bdcrops_GiftCard',
          __DIR__
      );
      ```
  </details>


- [GiftCard/etc/module.xml](etc/module.xml)
  <details><summary>Source</summary>
      ```
      <?xml version="1.0"?>
      <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
          <module name="Bdcrops_GiftCard" setup_version="1.0.0">
              <sequence>
                  <module name="Magento_Backend"/>
                  <module name="Magento_Catalog"/>
                  <module name="Magento_Checkout"/>
                  <module name="Magento_Quote"/>
                  <module name="Bdcrops_Core"/>
              </sequence>
          </module>
      </config>
      ```
  </details>
  
- [GiftCard/etc/product_types.xml](etc/product_types.xml)
  <details><summary>Source</summary>
    ```
    <?xml version="1.0"?>
    <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Catalog:etc/product_types.xsd">
        <type name="mpgiftcard" label="Gift Card Product" modelInstance="Bdcrops\GiftCard\Model\Product\Type\GiftCard" indexPriority="45" sortOrder="70" isQty="true">
            <priceModel instance="Bdcrops\GiftCard\Model\Product\Price"/>
            <customAttributes>
                <attribute name="refundable" value="true"/>
            </customAttributes>
        </type>
    </config>

    ```
  </details>

- [GiftCard/etc/acl.xml](etc/acl.xml)
  <details><summary>Source</summary>

      ```
      <?xml version="1.0"?>
      <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Acl/etc/acl.xsd">
          <acl>
              <resources>
                  <resource id="Magento_Backend::admin">
                      <resource id="Magento_Backend::marketing">
                          <resource id="Bdcrops_GiftCard::giftcard" title="Gift Card" translate="title" sortOrder="53">
                              <resource id="Bdcrops_GiftCard::code" title="Manage Gift Code" translate="title" sortOrder="20"/>
                              <resource id="Bdcrops_GiftCard::template" title="Manage Template" translate="title" sortOrder="30"/>
                              <resource id="Bdcrops_GiftCard::pool" title="Manage Gift Code Pool" translate="title" sortOrder="40"/>
                              <resource id="Bdcrops_GiftCard::history" title="Gift Card History" translate="title" sortOrder="50"/>
                              <resource id="Bdcrops_GiftCard::customer" title="Customer Gift Card Tab" translate="title" sortOrder="100"/>
                          </resource>
                      </resource>
                      <resource id="Magento_Backend::stores">
                          <resource id="Magento_Backend::stores_settings">
                              <resource id="Magento_Config::config">
                                  <resource id="Bdcrops_GiftCard::configuration" title="Gift Card"/>
                              </resource>
                          </resource>
                      </resource>
                  </resource>
              </resources>
          </acl>
      </config>
      ```
  </details>


- [GiftCard/etc/adminhtml/di.xml](etc/adminhtml/di.xml)
  <details><summary>Source</summary>

      ```
      <?xml version="1.0"?>
      <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
          <!--Override Quote coupon management to apply gift card from coupon block on checkout onepage-->
          <preference for="Magento\Quote\Api\CouponManagementInterface" type="Bdcrops\GiftCard\Model\Api\CouponManagement"/>

          <!--Use api to apply gift card/credit on checkout onpage-->
          <preference for="Bdcrops\GiftCard\Api\GiftCardManagementInterface" type="Bdcrops\GiftCard\Model\Api\GiftCardManagement"/>
          <preference for="Bdcrops\GiftCard\Api\GuestGiftCardManagementInterface" type="Bdcrops\GiftCard\Model\Api\GuestGiftCardManagement"/>

          <!--Parse float to fix zero price product in wishlist  -->
          <preference for="Magento\Catalog\Pricing\Price\ConfiguredRegularPrice" type="Bdcrops\GiftCard\Pricing\Price\ConfiguredRegularPrice"/>

          <!--Plugin cart total repository to add gift card used to totalsData-->
          <type name="Magento\Quote\Api\CartTotalRepositoryInterface">
              <plugin name="mp_gift_card_cart_total_repository_plugin" type="Bdcrops\GiftCard\Plugin\Quote\CartTotalRepository"/>
          </type>

          <!--Move item option from quote_item to order_item-->
          <type name="Magento\Quote\Model\Quote\Item\ToOrderItem">
              <plugin name="mp_gift_card_move_product_option_to_order_item" type="Bdcrops\GiftCard\Plugin\Quote\ToOrderItem"/>
          </type>

          <!--Check refundable gift card-->
          <type name="Magento\Sales\Model\Order\Item">
              <plugin name="mp_gift_card_check_refundable_gift_card" type="Bdcrops\GiftCard\Plugin\Quote\Item"/>
          </type>

          <!-- Run this command before remove module -->
          <type name="Magento\Framework\Console\CommandList">
              <arguments>
                  <argument name="commands" xsi:type="array">
                      <item name="bdcrops_giftcard_uninstall" xsi:type="object">Bdcrops\GiftCard\Console\Command\Uninstall</item>
                  </argument>
              </arguments>
          </type>

          <!--Grid collection-->
          <type name="Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory">
              <arguments>
                  <argument name="collections" xsi:type="array">
                      <item name="giftcard_code_listing_data_source" xsi:type="string">Bdcrops\GiftCard\Model\ResourceModel\GiftCard\Grid\Collection</item>
                      <item name="giftcard_template_listing_data_source" xsi:type="string">Bdcrops\GiftCard\Model\ResourceModel\Template\Grid\Collection</item>
                      <item name="giftcard_pool_listing_data_source" xsi:type="string">Bdcrops\GiftCard\Model\ResourceModel\Pool\Grid\Collection</item>
                      <item name="giftcard_history_listing_data_source" xsi:type="string">Bdcrops\GiftCard\Model\ResourceModel\History\Grid\Collection</item>
                  </argument>
              </arguments>
          </type>
          <virtualType name="Bdcrops\GiftCard\Model\ResourceModel\GiftCard\Grid\Collection" type="Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult">
              <arguments>
                  <argument name="mainTable" xsi:type="string">bdcrops_giftcard</argument>
                  <argument name="resourceModel" xsi:type="string">Bdcrops\GiftCard\Model\ResourceModel\GiftCard</argument>
              </arguments>
          </virtualType>
          <virtualType name="Bdcrops\GiftCard\Model\ResourceModel\Template\Grid\Collection" type="Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult">
              <arguments>
                  <argument name="mainTable" xsi:type="string">bdcrops_giftcard_template</argument>
                  <argument name="resourceModel" xsi:type="string">Bdcrops\GiftCard\Model\ResourceModel\Template</argument>
              </arguments>
          </virtualType>
          <virtualType name="Bdcrops\GiftCard\Model\ResourceModel\Pool\Grid\Collection" type="Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult">
              <arguments>
                  <argument name="mainTable" xsi:type="string">bdcrops_giftcard_pool</argument>
                  <argument name="resourceModel" xsi:type="string">Bdcrops\GiftCard\Model\ResourceModel\Pool</argument>
              </arguments>
          </virtualType>
          <virtualType name="Bdcrops\GiftCard\Model\ResourceModel\History\Grid\Collection" type="Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult">
              <arguments>
                  <argument name="mainTable" xsi:type="string">bdcrops_giftcard_history</argument>
                  <argument name="resourceModel" xsi:type="string">Bdcrops\GiftCard\Model\ResourceModel\History</argument>
              </arguments>
          </virtualType>
      </config>
      ```
  </details>

- [GiftCard/etc/adminhtml/events.xml](etc/adminhtml/events.xml)
  <details><summary>Source</summary>

      ```
      <?xml version="1.0"?>
      <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Event/etc/events.xsd">
          <event name="sales_model_service_quote_submit_before">
              <observer name="sales_convert_quote_giftcard" instance="Bdcrops\GiftCard\Observer\SalesConvertQuote"/>
          </event>
          <event name="sales_model_service_quote_submit_failure">
              <observer name="sales_revert_quote_giftcard" instance="Bdcrops\GiftCard\Observer\OrderCancel"/>
          </event>
          <event name="sales_order_save_after">
              <observer name="sales_order_save_after_giftcard" instance="Bdcrops\GiftCard\Observer\OrderSaveAfter"/>
          </event>
          <event name="sales_order_invoice_save_after">
              <observer name="sales_order_invoice_save_after_giftcard" instance="Bdcrops\GiftCard\Observer\InvoiceSaveAfter"/>
          </event>
          <event name="sales_order_creditmemo_save_after">
              <observer name="sales_order_creditmemo_save_after_giftcard" instance="Bdcrops\GiftCard\Observer\CreditmemoSaveAfter"/>
          </event>
          <event name="payment_cart_collect_items_and_amounts">
              <observer name="bdcrops_gift_card_papal_prepare" instance="Bdcrops\GiftCard\Observer\PaypalPrepareItems"/>
          </event>
      </config>

      ```
  </details>

- [GiftCard/etc/adminhtml/menu.xml](etc/adminhtml/menu.xml)
  <details><summary>Source</summary>

      ```
      <?xml version="1.0"?>
      <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Backend:etc/menu.xsd">
          <menu>
              <add id="Bdcrops_GiftCard::giftcard" title="Gift Card" module="Bdcrops_GiftCard" sortOrder="80" resource="Bdcrops_GiftCard::giftcard" parent="Magento_Backend::marketing"/>
              <add id="Bdcrops_GiftCard::code" title="Manage Gift Codes" module="Bdcrops_GiftCard" sortOrder="20" action="mpgiftcard/code" resource="Bdcrops_GiftCard::code" parent="Bdcrops_GiftCard::giftcard"/>
              <add id="Bdcrops_GiftCard::pool" title="Manage Gift Code Pools" module="Bdcrops_GiftCard" sortOrder="30" action="mpgiftcard/pool" resource="Bdcrops_GiftCard::pool" parent="Bdcrops_GiftCard::giftcard"/>
              <add id="Bdcrops_GiftCard::template" title="Manage Templates" module="Bdcrops_GiftCard" sortOrder="40" action="mpgiftcard/template" resource="Bdcrops_GiftCard::template" parent="Bdcrops_GiftCard::giftcard"/>
              <add id="Bdcrops_GiftCard::history" title="Gift Card History" module="Bdcrops_GiftCard" sortOrder="50" action="mpgiftcard/history" resource="Bdcrops_GiftCard::history" parent="Bdcrops_GiftCard::giftcard"/>
              <add id="Bdcrops_GiftCard::configuration" title="Configuration" module="Bdcrops_GiftCard" sortOrder="1000" action="adminhtml/system_config/edit/section/mpgiftcard" resource="Bdcrops_GiftCard::configuration" parent="Bdcrops_GiftCard::giftcard"/>
          </menu>
      </config>
      ```
  </details>


- [GiftCard/etc/adminhtml/routes.xml](etc/adminhtml/routes.xml)
  <details><summary>Source</summary>

      ```
      <?xml version="1.0"?>
      <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:App/etc/routes.xsd">
          <router id="admin">
              <route id="mpgiftcard" frontName="mpgiftcard">
                  <module name="Bdcrops_GiftCard" before="Magento_Backend"/>
              </route>
          </router>
      </config>
      ```
  </details>


- [GiftCard/etc/adminhtml/system.xml](etc/adminhtml/system.xml)
  <details><summary>Source</summary>

      ```
      <?xml version="1.0"?>
      <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Config:etc/system_file.xsd">
          <system>
              <section id="mpgiftcard" translate="label" sortOrder="50" showInDefault="1" showInWebsite="1" showInStore="1">
                  <class>separator-top</class>
                  <label>Gift Card</label>
                  <tab>bdcrops</tab>
                  <resource>Bdcrops_GiftCard::configuration</resource>
                  <group id="general" translate="label" type="text" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1">
                      <label>General Configuration</label>
                      <field id="enable" translate="label" type="select" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Module Enable</label>
                          <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                      </field>
                      <field id="pattern" translate="label" type="text" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Gift Code Pattern</label>
                          <comment><![CDATA[Default gift code pattern. Follow by this rule: </br>
                          <strong>[4A]</strong> - 4 alpha, <strong>[4N]</strong> - 4 numeric, <strong>[4AN]</strong> - 4 alphanumeric. </br>
                          For example: GIFT-[4AN]-[3A]-[5N] => <strong>GIFT-J34T-OEC-54354</strong>]]></comment>
                      </field>
                      <field id="enable_credit" translate="label" type="select" sortOrder="40" showInDefault="1" showInWebsite="1" showInStore="0">
                          <label>Enable Gift Card Credit</label>
                          <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                      </field>
                      <field id="can_redeem" translate="label" type="select" sortOrder="50" showInDefault="1" showInWebsite="1" showInStore="0">
                          <label>Gift Card Can Be Redeemed</label>
                          <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                          <depends>
                              <field id="enable_credit">1</field>
                          </depends>
                      </field>
                      <group id="hidden" translate="label" type="text" sortOrder="200" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Gift Code Hidden Configuration</label>
                          <frontend_model>Magento\Paypal\Block\Adminhtml\System\Config\Fieldset\Expanded</frontend_model>
                          <field id="enable" translate="label comment" type="select" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Enabled</label>
                              <comment>Show hidden code in the frontend</comment>
                              <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                          </field>
                          <field id="prefix" translate="label comment" type="text" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Prefix Chars</label>
                              <comment>The number of prefix characters will not be hidden</comment>
                          </field>
                          <field id="suffix" translate="label comment" type="text" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Suffix Chars</label>
                              <comment>The number of suffix characters will note be hidden</comment>
                          </field>
                          <field id="character" translate="label comment" type="text" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Hidden Character</label>
                              <comment>The character will replace the hidden code.</comment>
                          </field>
                      </group>
                  </group>
                  <group id="product" translate="label" type="text" sortOrder="50" showInDefault="1" showInWebsite="1" showInStore="1">
                      <label>Gift Card Product Configuration</label>
                      <field id="expire_after_day" translate="label" type="text" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="0">
                          <label>Gift Card Lifetime</label>
                      </field>
                      <field id="enable_delivery_date" translate="label" type="select" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Enable Delivery Date</label>
                          <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                      </field>
                      <field id="enable_timezone" translate="label" type="select" sortOrder="30" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Customer Can Select Timezone</label>
                          <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                          <depends>
                              <field id="enable_delivery_date">1</field>
                          </depends>
                      </field>
                      <group id="checkout" translate="label" type="text" sortOrder="200" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Buy Gift Card Product Process</label>
                          <frontend_model>Magento\Paypal\Block\Adminhtml\System\Config\Fieldset\Expanded</frontend_model>
                          <field id="item_renderer" translate="label" type="multiselect" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Gift Card Fields Show On Item</label>
                              <source_model>Bdcrops\GiftCard\Model\Source\FieldRenderer</source_model>
                          </field>
                          <field id="generate" translate="label" type="select" sortOrder="40" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Create Gift Code(s) When</label>
                              <source_model>Bdcrops\GiftCard\Model\Source\GenerateGiftCodeEvent</source_model>
                          </field>
                      </group>
                  </group>
                  <group id="checkout" translate="label" type="text" sortOrder="70" showInDefault="1" showInWebsite="1" showInStore="1">
                      <label>Gift Card/Credit Checkout Configuration</label>
                      <field id="used_coupon_box" translate="label comment" type="select" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Use Coupon Box To Apply Gift Card</label>
                          <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                          <comment>If not, another Gift Card box will be used.</comment>
                      </field>
                      <field id="used_multiple" translate="label" type="select" sortOrder="30" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Can Use Multiple Gift Cards</label>
                          <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                          <depends>
                              <field id="used_coupon_box">0</field>
                          </depends>
                      </field>
                      <field id="show_detail" translate="label" type="select" sortOrder="35" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Show Gift Card Summary On Total Block</label>
                          <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                      </field>
                      <field id="used_credit" translate="label" type="select" sortOrder="40" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Can Use Gift Credit</label>
                          <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                      </field>
                      <group id="process" translate="label" type="text" sortOrder="200" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Checkout Process</label>
                          <frontend_model>Magento\Paypal\Block\Adminhtml\System\Config\Fieldset\Expanded</frontend_model>
                          <field id="used_for_shipping" translate="label" type="select" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Can Use For Shipping Amount</label>
                              <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                          </field>
                          <field id="allow_refund" translate="label comment" type="select" sortOrder="50" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Allow Refund Gift Card</label>
                              <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                              <comment>If yes, Gift Card amount will be add back to its balance after order refund.</comment>
                          </field>
                      </group>
                  </group>
                  <group id="template" translate="label" type="text" sortOrder="80" showInDefault="1" showInWebsite="1" showInStore="1">
                      <label>Gift Card Template Configuration</label>
                      <field id="logo" translate="label comment" type="image" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Logo</label>
                          <backend_model>Magento\Config\Model\Config\Backend\Image</backend_model>
                          <upload_dir config="system/filesystem/media" scope_info="1">bdcrops/giftcard</upload_dir>
                          <base_url type="media" scope_info="1">bdcrops/giftcard</base_url>
                          <comment><![CDATA[Your default logo will be used on Gift Card (jpeg, gif, png)]]></comment>
                      </field>
                      <field id="message_max_char" translate="label comment" type="text" sortOrder="40" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Maximum Message Character</label>
                          <comment>If zero or empty, default '120' characters will be used.</comment>
                      </field>
                      <field id="note" translate="label" type="textarea" sortOrder="60" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Default Note</label>
                      </field>
                  </group>
                  <group id="email" translate="label" type="text" sortOrder="90" showInDefault="1" showInWebsite="1" showInStore="1">
                      <label>Email Configuration</label>
                      <field id="enable" translate="label" type="select" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Enable Email Notification</label>
                          <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                      </field>
                      <field id="sender" translate="label" type="select" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Email Sender</label>
                          <source_model>Magento\Config\Model\Config\Source\Email\Identity</source_model>
                      </field>
                      <field id="template" translate="label comment" type="select" sortOrder="30" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Gift Card Email Template</label>
                          <comment>Email sent to recipient.</comment>
                          <source_model>Magento\Config\Model\Config\Source\Email\Template</source_model>
                      </field>
                      <group id="update" translate="label" type="text" sortOrder="40" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Gift Card Update Notification</label>
                          <field id="enable" translate="label comment" type="select" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Enabled</label>
                              <comment>Send email to recipient when gift card is updated.</comment>
                              <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                          </field>
                          <field id="template" translate="label comment" type="select" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Gift Card Update Email Template</label>
                              <source_model>Magento\Config\Model\Config\Source\Email\Template</source_model>
                          </field>
                      </group>
                      <group id="before_expire" translate="label" type="text" sortOrder="50" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Before Expired Notification</label>
                          <field id="enable" translate="label" type="select" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Enabled</label>
                              <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                          </field>
                          <field id="template" translate="label comment" type="select" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Before Expired Email Template</label>
                              <source_model>Magento\Config\Model\Config\Source\Email\Template</source_model>
                          </field>
                          <field id="days" translate="label comment" type="text" sortOrder="30" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Before Expired Day(s)</label>
                              <comment>The number of day(s) before the gift card is expired.</comment>
                          </field>
                      </group>
                      <group id="notify_sender" translate="label" type="text" sortOrder="60" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Notify Sender After Gift Card is Sent</label>
                          <field id="enable" translate="label" type="select" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Enabled</label>
                              <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                          </field>
                          <field id="template" translate="label comment" type="select" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Sender Confirmation Email Template</label>
                              <source_model>Magento\Config\Model\Config\Source\Email\Template</source_model>
                          </field>
                      </group>
                      <group id="after_unused" translate="label" type="text" sortOrder="70" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Gift Card Unused Notification</label>
                          <field id="enable" translate="label comment" type="select" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Enabled</label>
                              <comment>Inform the sender after x day(s) if the gift card is unused.</comment>
                              <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                          </field>
                          <field id="template" translate="label comment" type="select" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Unused Notification Email Template</label>
                              <source_model>Magento\Config\Model\Config\Source\Email\Template</source_model>
                          </field>
                          <field id="days" translate="label comment" type="text" sortOrder="30" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>After Unused Day(s)</label>
                              <comment>The number of day(s) after the gift card is sent to recipient.</comment>
                          </field>
                      </group>
                      <group id="credit" translate="label" type="text" sortOrder="80" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Customer Balance Update Notification</label>
                          <field id="enable" translate="label" type="select" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Enabled</label>
                              <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                          </field>
                          <field id="template" translate="label comment" type="select" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Update Balance Email Template</label>
                              <source_model>Magento\Config\Model\Config\Source\Email\Template</source_model>
                          </field>
                      </group>
                  </group>
                  <group id="sms" translate="label" type="text" sortOrder="100" showInDefault="1" showInWebsite="1" showInStore="1">
                      <label>SMS Configuration</label>
                      <field id="enable" translate="label comment" type="select" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Enable SMS Delivery</label>
                          <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                          <comment>If Yes, the purchaser can use this method to send gift card.</comment>
                      </field>
                      <field id="twilio_account_sid" translate="label comment" type="text" sortOrder="300" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Twilio Account SID</label>
                          <comment><![CDATA[Sign up an account <a href="https://www.twilio.com/console" target="_blank">here</a>]]></comment>
                      </field>
                      <field id="twilio_account_token" translate="label" type="password" sortOrder="400" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Twilio Account Token</label>
                      </field>
                      <field id="address_sender" translate="label comment" type="text" sortOrder="500" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Sender Phone Number</label>
                          <comment><![CDATA[You can buy and get the phone number <a href="https://www.twilio.com/console/phone-numbers/search" target="_blank">here</a>. <i>Example: +12172671060</i>]]></comment>
                      </field>
                      <field id="content" translate="label" type="textarea" sortOrder="600" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>SMS Message</label>
                          <comment><![CDATA[
                              <b>Variables:</b><br>
                              <p style="margin-left: 20px; font-size: 12px">
                                  <b>{{sender_name}}:</b> Sender name<br>
                                  <b>{{code}}:</b> Gift Card Code<br>
                                  <b>{{message}}:</b> Gift Card Message<br>
                                  <b>{{balance}}:</b> Gift Card balance<br>
                                  <b>{{status}}:</b> Gift Card status<br>
                                  <b>{{expired_date}}:</b> Gift Card expired date<br>
                                  <b>{{store_url}}:</b> Store Url<br>
                              </p>
                              ]]>
                          </comment>
                      </field>
                      <group id="update" translate="label" type="text" sortOrder="900" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Gift Card Update Notification</label>
                          <field id="enable" translate="label comment" type="select" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Enabled</label>
                              <comment>Send sms to recipient when gift card is updated.</comment>
                              <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                          </field>
                          <field id="content" translate="label comment" type="textarea" sortOrder="600" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>SMS Message</label>
                          </field>
                      </group>
                      <group id="before_expire" translate="label" type="text" sortOrder="1000" showInDefault="1" showInWebsite="1" showInStore="1">
                          <label>Before Expired Notification</label>
                          <field id="enable" translate="label comment" type="select" sortOrder="10" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Enabled</label>
                              <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                              <comment>The before expired day(s) will be get from Email Before Expired Day(s) configuration.</comment>
                          </field>
                          <field id="content" translate="label comment" type="textarea" sortOrder="20" showInDefault="1" showInWebsite="1" showInStore="1">
                              <label>Message Message</label>
                          </field>
                      </group>
                  </group>
              </section>
          </system>
      </config>
      ```
  </details>

- [GiftCard/etc/catalog_attributes.xml](etc/catalog_attributes.xml)
  <details><summary>Source</summary>

      ```
      <?xml version="1.0"?>
      <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Catalog:etc/catalog_attributes.xsd">
          <group name="quote_item">
              <attribute name="gift_code_pattern"/>
              <attribute name="gift_card_type"/>
              <attribute name="gift_card_amounts"/>
              <attribute name="allow_amount_range"/>
              <attribute name="min_amount"/>
              <attribute name="max_amount"/>
              <attribute name="price_rate"/>
              <attribute name="can_redeem"/>
              <attribute name="expire_after_day"/>
              <attribute name="gift_product_template"/>
          </group>
          <group name="catalog_product">
              <attribute name="gift_code_pattern"/>
              <attribute name="gift_card_type"/>
              <attribute name="gift_card_amounts"/>
              <attribute name="allow_amount_range"/>
              <attribute name="min_amount"/>
              <attribute name="max_amount"/>
              <attribute name="price_rate"/>
              <attribute name="can_redeem"/>
              <attribute name="expire_after_day"/>
              <attribute name="gift_product_template"/>
          </group>
          <group name="unassignable">
              <attribute name="allow_amount_range"/>
              <attribute name="gift_card_amounts"/>
              <attribute name="gift_card_type"/>
          </group>
      </config>
      ```
  </details>


- [GiftCard/etc/config.xml](etc/config.xml)
  <details><summary>Source</summary>

      ```

      ```
  </details>

- [GiftCard/etc/crontab.xml](etc/crontab.xml)
  <details><summary>Source</summary>

      ```

      ```
  </details>

- [GiftCard/etc/di.xml](etc/di.xml)
  <details><summary>Source</summary>

      ```

      ```
  </details>

- [GiftCard/etc/email_templates.xml](etc/email_templates.xml)
  <details><summary>Source</summary>

      ```

      ```
  </details>

- [GiftCard/etc/events.xml](etc/events.xml)
  <details><summary>Source</summary>

      ```

      ```
  </details>

- [GiftCard/etc/extension_attributes.xml](etc/extension_attributes.xml)
  <details><summary>Source</summary>

      ```

      ```
  </details>

- [GiftCard/etc/frontend/di.xml](etc/frontend/di.xml)
  <details><summary>Source</summary>

      ```

      ```
  </details>

- [GiftCard/etc/frontend/events.xml](etc/frontend/events.xml)
  <details><summary>Source</summary>

      ```

      ```
  </details>

- [GiftCard/etc/frontend/routes.xml](etc/frontend/routes.xml)
  <details><summary>Source</summary>

      ```

      ```
  </details>

- [GiftCard/etc/import.xml](etc/import.xml)
  <details><summary>Source</summary>

      ```

      ```
  </details>

- [GiftCard/etc/pdf.xml](etc/pdf.xml)
  <details><summary>Source</summary>

      ```

      ```
  </details>

- [GiftCard/etc/product_types.xml](etc/product_types.xml)
  <details><summary>Source</summary>

      ```

      ```
  </details>

- [GiftCard/etc/sales.xml](etc/sales.xml)
  <details><summary>Source</summary>

      ```

      ```
  </details>

- [GiftCard/etc/webapi.xml](etc/webapi.xml)
  <details><summary>Source</summary>

    ```

    ```
  </details>


- [GiftCard/Api/GiftCardManagementInterface.php](Api/GiftCardManagementInterface.php)
- [GiftCard/Api/GuestGiftCardManagementInterface.php](Api/GuestGiftCardManagementInterface.php)

- [GiftCard/Block/Adminhtml/Customer/Edit/Tab/Balance.php]()
- [GiftCard/Block/Adminhtml/Customer/Edit/Tab/GiftCard.php]()
- [GiftCard/Block/Adminhtml/Customer/Edit/Tab/Transaction.php]()
- [GiftCard/Block/Adminhtml/Customer/Edit/Tab/Transaction/DetailRenderer.php]()
- [GiftCard/Block/Adminhtml/GiftCard/Edit.php]()
- [GiftCard/Block/Adminhtml/GiftCard/Edit/Form.php]()
- [GiftCard/Block/Adminhtml/GiftCard/Edit/Tab/History.php]()
- [GiftCard/Block/Adminhtml/GiftCard/Edit/Tab/History/DetailRenderer.php]()
- [GiftCard/Block/Adminhtml/GiftCard/Edit/Tab/Information.php]()
- [GiftCard/Block/Adminhtml/GiftCard/Edit/Tab/Send.php]()
- [GiftCard/Block/Adminhtml/GiftCard/Edit/Tabs.php]()
- [GiftCard/Block/Adminhtml/Grid/Column/Renderer/Price.php]()
- [GiftCard/Block/Adminhtml/Order/Create/Giftcard.php]()
- [GiftCard/Block/Adminhtml/Order/Items/Giftcard.php]()
- [GiftCard/Block/Adminhtml/Order/Total/Discount.php]()
- [GiftCard/Block/Adminhtml/Pool/Edit.php]()
- [GiftCard/Block/Adminhtml/Pool/Edit/Form.php]()
- [GiftCard/Block/Adminhtml/Pool/Edit/Tab/Generate.php]()
- [GiftCard/Block/Adminhtml/Pool/Edit/Tab/Generate/Form.php]()
- [GiftCard/Block/Adminhtml/Pool/Edit/Tab/Generate/Grid.php]()
- [GiftCard/Block/Adminhtml/Pool/Edit/Tab/Information.php]()
- [GiftCard/Block/Adminhtml/Pool/Edit/Tabs.php]()
- [GiftCard/Block/Adminhtml/Product/Composite/Fieldset/GiftCard.php]()
- [GiftCard/Block/Adminhtml/Product/Helper/Form/Config/Pattern.php]()
- [GiftCard/Block/Adminhtml/Template/Edit.php]()
- [GiftCard/Block/Adminhtml/Template/Edit/Form.php]()
- [GiftCard/Block/Adminhtml/Template/Edit/Tab/Design.php]()
- [GiftCard/Block/Adminhtml/Template/Edit/Tab/Images.php]()
- [GiftCard/Block/Adminhtml/Template/Edit/Tab/Information.php]()
- [GiftCard/Block/Adminhtml/Template/Edit/Tab/Renderer/Images.php]()
- [GiftCard/Block/Adminhtml/Template/Edit/Tabs.php]()
- [GiftCard/Block/Checkout/Item/Renderer.php]()
- [GiftCard/Block/Dashboard.php]()
- [GiftCard/Block/Product/View.php]()
- [GiftCard/Block/Sales/Item/Renderer.php]()
- [GiftCard/Block/Sales/Order/Discount.php]()

- [GiftCard/Console/Command/Uninstall.php]()
- [GiftCard/Controller/AbstractGiftcard.php]()
- [GiftCard/Controller/Adminhtml/Code.php]()
- [GiftCard/Controller/Adminhtml/Code/Delete.php]()
- [GiftCard/Controller/Adminhtml/Code/Edit.php]()
- [GiftCard/Controller/Adminhtml/Code/History.php]()
- [GiftCard/Controller/Adminhtml/Code/Index.php]()
- [GiftCard/Controller/Adminhtml/Code/MassDelete.php]()
- [GiftCard/Controller/Adminhtml/Code/MassPrintPDF.php]()
- [GiftCard/Controller/Adminhtml/Code/MassSend.php]()
- [GiftCard/Controller/Adminhtml/Code/MassStatus.php]()
- [GiftCard/Controller/Adminhtml/Code/NewAction.php]()
- [GiftCard/Controller/Adminhtml/Code/Save.php]()
- [GiftCard/Controller/Adminhtml/Customer/Change.php]()
- [GiftCard/Controller/Adminhtml/Customer/GiftCard.php]()
- [GiftCard/Controller/Adminhtml/Customer/Grid.php]()
- [GiftCard/Controller/Adminhtml/History/Index.php]()
- [GiftCard/Controller/Adminhtml/Pool.php]()
- [GiftCard/Controller/Adminhtml/Pool/CardsMassDelete.php]()
- [GiftCard/Controller/Adminhtml/Pool/CardsMassPrint.php]()
- [GiftCard/Controller/Adminhtml/Pool/Edit.php]()
- [GiftCard/Controller/Adminhtml/Pool/ExportCouponsCsv.php]()
- [GiftCard/Controller/Adminhtml/Pool/ExportCouponsXml.php]()
- [GiftCard/Controller/Adminhtml/Pool/Generate.php]()
- [GiftCard/Controller/Adminhtml/Pool/Grid.php]()
- [GiftCard/Controller/Adminhtml/Pool/Index.php]()
- [GiftCard/Controller/Adminhtml/Pool/MassDelete.php]()
- [GiftCard/Controller/Adminhtml/Pool/MassStatus.php]()
- [GiftCard/Controller/Adminhtml/Pool/NewAction.php]()
- [GiftCard/Controller/Adminhtml/Pool/Save.php]()
- [GiftCard/Controller/Adminhtml/Template.php]()
- [GiftCard/Controller/Adminhtml/Template/Delete.php]()
- [GiftCard/Controller/Adminhtml/Template/Edit.php]()
- [GiftCard/Controller/Adminhtml/Template/Index.php]()
- [GiftCard/Controller/Adminhtml/Template/MassDelete.php]()
- [GiftCard/Controller/Adminhtml/Template/MassStatus.php]()
- [GiftCard/Controller/Adminhtml/Template/NewAction.php]()
- [GiftCard/Controller/Adminhtml/Template/Save.php]()
- [GiftCard/Controller/Adminhtml/Template/Upload.php]()
- [GiftCard/Controller/Index/AddList.php]()
- [GiftCard/Controller/Index/Check.php]()
- [GiftCard/Controller/Index/Cron.php]()
- [GiftCard/Controller/Index/Index.php]()
- [GiftCard/Controller/Index/PrintPDF.php]()
- [GiftCard/Controller/Index/Redeem.php]()
- [GiftCard/Controller/Index/Settings.php]()
- [GiftCard/Controller/Template/Upload.php]()
- [GiftCard/Cron/Notification.php]()
- [GiftCard/Cron/Process.php]()
- [GiftCard/Cron/RemoveTmpImages.php]()
- [GiftCard/Helper/Checkout.php]()
- [GiftCard/Helper/Data.php]()
- [GiftCard/Helper/Email.php]()
- [GiftCard/Helper/Product.php]()
- [GiftCard/Helper/Sms.php]()
- [GiftCard/Helper/Template.php]()
- [GiftCard/Mail/Template/TransportBuilder.php]()
- [GiftCard/Model/Api/CouponManagement.php]()
- [GiftCard/Model/Api/GiftCardManagement.php]()
- [GiftCard/Model/Api/GuestGiftCardManagement.php]()
- [GiftCard/Model/Attribute/Backend/AbstractClass.php]()
- [GiftCard/Model/Attribute/Backend/Amount.php]()
- [GiftCard/Model/Attribute/Backend/MultiSelect.php]()
- [GiftCard/Model/Attribute/Backend/Pattern.php]()
- [GiftCard/Model/Credit.php]()
- [GiftCard/Model/GiftCard.php]()
- [GiftCard/Model/GiftCard/Action.php]()
- [GiftCard/Model/GiftCard/Status.php]()
- [GiftCard/Model/GiftCard/Template.php]()
- [GiftCard/Model/History.php]()
- [GiftCard/Model/Import/GiftCard.php]()
- [GiftCard/Model/Import/GiftCard/RowValidatorInterface.php]()
- [GiftCard/Model/Pool.php]()
- [GiftCard/Model/Product/DeliveryMethods.php]()
- [GiftCard/Model/Product/Price.php]()
- [GiftCard/Model/Product/Type/GiftCard.php]()
- [GiftCard/Model/Product/Validator/PhoneNumber.php]()
- [GiftCard/Model/ResourceModel/Credit.php]()
- [GiftCard/Model/ResourceModel/Credit/Collection.php]()
- [GiftCard/Model/ResourceModel/GiftCard.php]()
- [GiftCard/Model/ResourceModel/GiftCard/Collection.php]()
- [GiftCard/Model/ResourceModel/History.php]()
- [GiftCard/Model/ResourceModel/History/Collection.php]()
- [GiftCard/Model/ResourceModel/Pool.php]()
- [GiftCard/Model/ResourceModel/Pool/Collection.php]()
- [GiftCard/Model/ResourceModel/Template.php]()
- [GiftCard/Model/ResourceModel/Template/Collection.php]()
- [GiftCard/Model/ResourceModel/Transaction.php]()
- [GiftCard/Model/ResourceModel/Transaction/Collection.php]()
- [GiftCard/Model/Source/AllowRefund.php]()
- [GiftCard/Model/Source/DeliveryMethods.php]()
- [GiftCard/Model/Source/FieldRenderer.php]()
- [GiftCard/Model/Source/Fonts.php]()
- [GiftCard/Model/Source/GenerateGiftCodeEvent.php]()
- [GiftCard/Model/Source/Status.php]()
- [GiftCard/Model/Template.php]()
- [GiftCard/Model/Total/Creditmemo/Discount.php]()
- [GiftCard/Model/Total/Invoice/Discount.php]()
- [GiftCard/Model/Total/Quote/Discount.php]()
- [GiftCard/Model/Transaction.php]()
- [GiftCard/Model/Transaction/Action.php]()
- [GiftCard/Observer/CouponPost.php]()
- [GiftCard/Observer/CreditmemoSaveAfter.php]()
- [GiftCard/Observer/InvoiceSaveAfter.php]()
- [GiftCard/Observer/OrderCancel.php]()
- [GiftCard/Observer/OrderCreateProcessData.php]()
- [GiftCard/Observer/OrderSaveAfter.php]()
- [GiftCard/Observer/PaypalPrepareItems.php]()
- [GiftCard/Observer/SalesConvertQuote.php]()
- [GiftCard/Plugin/Block/CartCoupon.php]()
- [GiftCard/Plugin/Block/Order/Create/Coupons.php]()
- [GiftCard/Plugin/Controller/ImportDownloadSample.php]()
- [GiftCard/Plugin/Controller/Product/Builder.php]()
- [GiftCard/Plugin/Model/Order/Creditmemo/Item.php]()
- [GiftCard/Plugin/Quote/CartTotalRepository.php]()
- [GiftCard/Plugin/Quote/Item.php]()
- [GiftCard/Plugin/Quote/ToOrderItem.php]()
- [GiftCard/Pricing/Price/ConfiguredRegularPrice.php]()
- [GiftCard/Pricing/Render/FinalPriceBox.php]()
- [GiftCard/Setup/InstallData.php]()
- [GiftCard/Setup/InstallSchema.php]()
- [GiftCard/Setup/Uninstall.php]()
- [GiftCard/Setup/UpgradeData.php]()
- [GiftCard/Ui/Component/Listing/Columns/Actions.php]()
- [GiftCard/Ui/Component/Listing/Columns/Amount.php]()
- [GiftCard/Ui/Component/Listing/Columns/HistoryContent.php]()
- [GiftCard/Ui/Component/Listing/Columns/Name.php]()
- [GiftCard/Ui/Component/Listing/Columns/PoolAvailable.php]()
- [GiftCard/Ui/Component/Listing/Columns/Thumbnail.php]()
- [GiftCard/Ui/DataProvider/Product/Modifier/GiftCard.php]()

- [GiftCard/view/adminhtml/layout/catalog_product_view_type_mpgiftcard.xml]()
- [GiftCard/view/adminhtml/layout/customer_index_edit.xml]()
- [GiftCard/view/adminhtml/layout/mpgiftcard_code_edit.xml]()
- [GiftCard/view/adminhtml/layout/mpgiftcard_code_history.xml]()
- [GiftCard/view/adminhtml/layout/mpgiftcard_code_index.xml]()
- [GiftCard/view/adminhtml/layout/mpgiftcard_customer_giftcard.xml]()
- [GiftCard/view/adminhtml/layout/mpgiftcard_customer_grid.xml]()
- [GiftCard/view/adminhtml/layout/mpgiftcard_history_index.xml]()
- [GiftCard/view/adminhtml/layout/mpgiftcard_pool_edit.xml]()
- [GiftCard/view/adminhtml/layout/mpgiftcard_pool_grid.xml]()
- [GiftCard/view/adminhtml/layout/mpgiftcard_pool_index.xml]()
- [GiftCard/view/adminhtml/layout/mpgiftcard_pool_managecodes.xml]()
- [GiftCard/view/adminhtml/layout/mpgiftcard_template_edit.xml]()
- [GiftCard/view/adminhtml/layout/mpgiftcard_template_index.xml]()
- [GiftCard/view/adminhtml/layout/sales_order_create_index.xml]()
- [GiftCard/view/adminhtml/layout/sales_order_create_load_block_data.xml]()
- [GiftCard/view/adminhtml/layout/sales_order_create_load_block_items.xml]()
- [GiftCard/view/adminhtml/layout/sales_order_creditmemo_new.xml]()
- [GiftCard/view/adminhtml/layout/sales_order_creditmemo_updateqty.xml]()
- [GiftCard/view/adminhtml/layout/sales_order_creditmemo_view.xml]()
- [GiftCard/view/adminhtml/layout/sales_order_invoice_new.xml]()
- [GiftCard/view/adminhtml/layout/sales_order_invoice_updateqty.xml]()
- [GiftCard/view/adminhtml/layout/sales_order_invoice_view.xml]()
- [GiftCard/view/adminhtml/layout/sales_order_view.xml]()
- [GiftCard/view/adminhtml/requirejs-config.js]()
- [GiftCard/view/adminhtml/templates/catalog/product/composite/fieldset/giftcard.phtml]()
- [GiftCard/view/adminhtml/templates/customer/balance.phtml]()
- [GiftCard/view/adminhtml/templates/order/create/giftcard.phtml]()
- [GiftCard/view/adminhtml/templates/pool/generate.phtml]()
- [GiftCard/view/adminhtml/templates/pool/js.phtml]()
- [GiftCard/view/adminhtml/templates/template/design.phtml]()
- [GiftCard/view/adminhtml/templates/template/gallery.phtml]()
- [GiftCard/view/adminhtml/ui_component/giftcard_code_listing.xml]()
- [GiftCard/view/adminhtml/ui_component/giftcard_history_listing.xml]()
- [GiftCard/view/adminhtml/ui_component/giftcard_pool_listing.xml]()
- [GiftCard/view/adminhtml/ui_component/giftcard_template_listing.xml]()

- [GiftCard/view/adminhtml/web/css/source/_ module.less]()

- [GiftCard/view/adminhtml/web/js/form/element/allow-amount-range.js]()
- [GiftCard/view/adminhtml/web/js/form/element/text-use-config.js]()
- [GiftCard/view/adminhtml/web/js/gift_card.js]()
- [GiftCard/view/adminhtml/web/js/grid/columns/delivery.js]()
- [GiftCard/view/adminhtml/web/js/grid/columns/thumbnail.js]()
- [GiftCard/view/adminhtml/web/js/model/product.js]()
- [GiftCard/view/adminhtml/web/js/view/design.js]()
- [GiftCard/view/adminhtml/web/js/view/image-gallery.js]()
- [GiftCard/view/adminhtml/web/js/view/order/create.js]()
- [GiftCard/view/adminhtml/web/template/form/design/field.html]()
- [GiftCard/view/adminhtml/web/template/grid/cells/thumbnail/preview.html]()
- [GiftCard/view/base/layout/catalog_product_prices.xml]()
- [GiftCard/view/base/templates/product/price/final_price.phtml]()
- [GiftCard/view/base/web/fonts/Roboto-Black.ttf
- [GiftCard/view/base/web/fonts/Roboto-BlackItalic.ttf
- [GiftCard/view/base/web/fonts/Roboto-Bold.ttf
- [GiftCard/view/base/web/fonts/baloobhai.ttf
- [GiftCard/view/base/web/fonts/bigapple.ttf
- [GiftCard/view/base/web/fonts/victoria.ttf
- [GiftCard/view/base/web/fonts/zapfchmi.ttf
- [GiftCard/view/base/web/images/barcode.png
- [GiftCard/view/base/web/images/x-mark.png
- [GiftCard/view/frontend/email/before_expired.html]()
- [GiftCard/view/frontend/email/credit_update.html]()
- [GiftCard/view/frontend/email/giftcard_template.html]()
- [GiftCard/view/frontend/email/giftcard_update.html]()
- [GiftCard/view/frontend/email/notify_sender.html]()
- [GiftCard/view/frontend/email/notify_unused.html]()
- [GiftCard/view/frontend/layout/catalog_product_view_type_mpgiftcard.xml]()
- [GiftCard/view/frontend/layout/checkout_cart_configure_type_mpgiftcard.xml]()
- [GiftCard/view/frontend/layout/checkout_cart_index.xml]()
- [GiftCard/view/frontend/layout/checkout_cart_item_renderers.xml]()
- [GiftCard/view/frontend/layout/checkout_index_index.xml]()
- [GiftCard/view/frontend/layout/customer_account.xml]()
- [GiftCard/view/frontend/layout/default.xml]()
- [GiftCard/view/frontend/layout/mpgiftcard_index_index.xml]()
- [GiftCard/view/frontend/layout/sales_email_order_creditmemo_items.xml]()
- [GiftCard/view/frontend/layout/sales_email_order_invoice_items.xml]()
- [GiftCard/view/frontend/layout/sales_email_order_items.xml]()
- [GiftCard/view/frontend/layout/sales_email_order_renderers.xml]()
- [GiftCard/view/frontend/layout/sales_guest_creditmemo.xml]()
- [GiftCard/view/frontend/layout/sales_guest_invoice.xml]()
- [GiftCard/view/frontend/layout/sales_guest_print.xml]()
- [GiftCard/view/frontend/layout/sales_guest_printcreditmemo.xml]()
- [GiftCard/view/frontend/layout/sales_guest_printinvoice.xml]()
- [GiftCard/view/frontend/layout/sales_guest_view.xml]()
- [GiftCard/view/frontend/layout/sales_order_creditmemo.xml]()
- [GiftCard/view/frontend/layout/sales_order_invoice.xml]()
- [GiftCard/view/frontend/layout/sales_order_item_renderers.xml]()
- [GiftCard/view/frontend/layout/sales_order_print.xml]()
- [GiftCard/view/frontend/layout/sales_order_print_renderers.xml]()
- [GiftCard/view/frontend/layout/sales_order_printcreditmemo.xml]()
- [GiftCard/view/frontend/layout/sales_order_printinvoice.xml]()
- [GiftCard/view/frontend/layout/sales_order_view.xml]()
- [GiftCard/view/frontend/layout/wishlist_index_configure_type_mpgiftcard.xml]()
- [GiftCard/view/frontend/templates/cart/coupon.phtml]()
- [GiftCard/view/frontend/templates/dashboard.phtml]()
- [GiftCard/view/frontend/templates/product/gallery.phtml]()
- [GiftCard/view/frontend/templates/product/view.phtml]()
- [GiftCard/view/frontend/web/css/source/_ module.less]()
- [GiftCard/view/frontend/web/js/action/add-list.js]()
- [GiftCard/view/frontend/web/js/action/apply-credit.js]()
- [GiftCard/view/frontend/web/js/action/apply-gift-card.js]()
- [GiftCard/view/frontend/web/js/action/cancel-gift-card.js]()
- [GiftCard/view/frontend/web/js/action/check-code-availability.js]()
- [GiftCard/view/frontend/web/js/action/redeem.js]()
- [GiftCard/view/frontend/web/js/action/save-settings.js]()
- [GiftCard/view/frontend/web/js/model/checkout.js]()
- [GiftCard/view/frontend/web/js/model/dashboard.js]()
- [GiftCard/view/frontend/web/js/model/messageList.js]()
- [GiftCard/view/frontend/web/js/model/product.js]()
- [GiftCard/view/frontend/web/js/model/resource-url-manager.js]()
- [GiftCard/view/frontend/web/js/view/checkout.js]()
- [GiftCard/view/frontend/web/js/view/dashboard.js]()
- [GiftCard/view/frontend/web/js/view/information.js]()
- [GiftCard/view/frontend/web/js/view/messages.js]()
- [GiftCard/view/frontend/web/js/view/template.js]()
- [GiftCard/view/frontend/web/js/view/totals/discount.js]()
- [GiftCard/view/frontend/web/template/check.html]()
- [GiftCard/view/frontend/web/template/checkout/cart.html]()
- [GiftCard/view/frontend/web/template/checkout/payment.html]()
- [GiftCard/view/frontend/web/template/dashboard.html]()
- [GiftCard/view/frontend/web/template/list/view.html]()
- [GiftCard/view/frontend/web/template/messages.html]()
- [GiftCard/view/frontend/web/template/product/gallery.html]()
- [GiftCard/view/frontend/web/template/product/view.html]()
- [GiftCard/view/frontend/web/template/totals/discount.html]()


## Ref
