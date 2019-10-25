<?php

namespace Bdcrops\CatalogRuleApi\Api\Data;

interface RuleExtensionInterface {
    /**
     * @return \Magento\CatalogRule\Api\Data\RuleExtensionInterface
     */
    public function getWebsiteIds();
   /**
    * @param \Magento\CatalogRule\Api\Data\RuleExtensionInterface[]
    * @return $this
    */
   public function setWebsiteIds();
  /**
    * @return \Magento\CatalogRule\Api\Data\RuleExtensionInterface
    */
    public function getCustomerGroupIds();
   /**
    * @param \Magento\CatalogRule\Api\Data\RuleExtensionInterface[]
    * @return $this
    */
	public function setCustomerGroupIds();

}
