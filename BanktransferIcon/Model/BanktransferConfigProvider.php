<?php
declare(strict_types=1);

namespace Bdcrops\BanktransferIcon\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository;

class BanktransferConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Repository
     */
    private $assetRepository;

    /**
     * BanktransferConfigProvider constructor.
     *
     * @param Repository $assetRepository
     */
    public function __construct(
        Repository $assetRepository
    ) {
        $this->assetRepository = $assetRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $banktransferLogoUrl = $this->assetRepository->getUrlWithParams('Bdcrops_BanktransferIcon::images/banktransfer.png', []);
        return [
            'payment' => [
                'banktransfer' => [
                    'banktransferLogoUrl' => $banktransferLogoUrl
                ]
            ]
        ];
    }
}
