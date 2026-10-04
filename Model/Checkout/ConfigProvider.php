<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Panth\ZipcodeValidation\ViewModel\FrontendConfig;

class ConfigProvider implements ConfigProviderInterface
{
    private FrontendConfig $frontendConfig;

    public function __construct(FrontendConfig $frontendConfig)
    {
        $this->frontendConfig = $frontendConfig;
    }

    public function getConfig()
    {
        return ['panthZipcodeValidation' => $this->frontendConfig->getCheckoutConfig()];
    }
}
