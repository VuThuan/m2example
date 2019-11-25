<?php
declare(strict_types=1);

namespace Bdcrops\GraphQLStorePickup\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\ConfigInterface;
use Magento\Framework\GraphQl\Query\Resolver\Argument\FieldEntityAttributesInterface;

class FilterArgument implements FieldEntityAttributesInterface {
    /** @var ConfigInterface */
    private $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function getEntityAttributes(): array
    {
        $fields = [];
        /** @var Field $field */
        foreach ($this->config->getConfigElement('PickUpStore')->getFields() as $field) {
            $fields[$field->getName()] = '';
        }

        return array_keys($fields);
    }
}
