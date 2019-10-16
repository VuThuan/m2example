<?php
namespace Bdcrops\CatalogWidget\Model\Config\Source;

class SortBy implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'name', 'label' => __('Product Name')],
            ['value' => 'price', 'label' => __('Price')]
        ];
    }
}
