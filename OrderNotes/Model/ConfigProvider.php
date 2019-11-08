<?php
namespace BDC\OrderNotes\Model;

use \Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface {
    public function getConfig() {
        return [
            'orderNotes' => [
                'title' => __('Order Notes'),
                'header' => __('Header content.'),
                'footer' => __('Footer content.'),
                'options' => [
                    [ 'code' => 'ring', 'value' => __('Ring longer') ],
                    [ 'code' => 'backyard', 'value' => __('Try backyard') ],
                    [ 'code' => 'neighbour', 'value' => __('Ping neighbour') ],
                    [ 'code' => 'other', 'value' => __('Other') ],
                ],
                'time' => (new \DateTime('now'))->format('Y-m-d H:i:s')
            ]
        ];
    }
}
