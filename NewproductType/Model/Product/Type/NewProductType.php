<?php
namespace Bdcrops\NewproductType\Model\Product\Type;
class NewProductType extends MagentoCatalogModelProductTypeAbstractType
{
const TYPE_CODE = 'new_product_type';
 public function save($product)
 {
    parent::save($product);
    // your additional saving logic
   return $this;
 }
 public function deleteTypeSpecificData(MagentoCatalogModelProduct $product)
 {
   //your deleting logic
 }
}
