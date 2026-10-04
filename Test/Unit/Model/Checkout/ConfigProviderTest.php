<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Test\Unit\Model\Checkout;

use Panth\ZipcodeValidation\Model\Checkout\ConfigProvider;
use Panth\ZipcodeValidation\ViewModel\FrontendConfig;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    public function testCheckoutSettingsAreExposedUnderTheModuleKey(): void
    {
        $settings = ['enabled' => true, 'showSuccess' => false, 'successFormat' => 'OK {state}'];
        $frontendConfig = $this->createMock(FrontendConfig::class);
        $frontendConfig->expects($this->once())->method('getCheckoutConfig')->willReturn($settings);

        $this->assertSame(['panthZipcodeValidation' => $settings], (new ConfigProvider($frontendConfig))->getConfig());
    }
}
