<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Test\Unit\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\UrlInterface;
use Panth\ZipcodeValidation\Model\Checkout\ConfigProvider;
use Panth\ZipcodeValidation\ViewModel\FrontendConfig;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FrontendConfigTest extends TestCase
{
    private function viewModel(array $flags = [], array $values = [], string $action = ''): FrontendConfig
    {
        $config = $this->createStub(ScopeConfigInterface::class);
        $config->method('isSetFlag')->willReturnCallback(static fn($path) => !empty($flags[$path]));
        $config->method('getValue')->willReturnCallback(static fn($path) => $values[$path] ?? null);

        $request = $this->createStub(Http::class);
        $request->method('getFullActionName')->willReturn($action);

        $url = $this->createStub(UrlInterface::class);
        $url->method('getUrl')->willReturnCallback(static fn($route) => 'https://shop.test/' . $route);

        return new FrontendConfig($config, $request, $url);
    }

    public function testInactiveWhenDisabled(): void
    {
        $viewModel = $this->viewModel([], [], 'cms_index_index');

        $this->assertFalse($viewModel->isEnabled());
        $this->assertFalse($viewModel->isActiveOnPage());
    }

    #[DataProvider('pageCases')]
    public function testPageSpecificSwitches(string $action, string $flag): void
    {
        $base = [FrontendConfig::XML_PATH_ENABLED => true];

        $this->assertFalse($this->viewModel($base, [], $action)->isActiveOnPage());
        $this->assertTrue($this->viewModel($base + [$flag => true], [], $action)->isActiveOnPage());
    }

    public static function pageCases(): array
    {
        return [
            'address edit' => ['customer_address_edit', FrontendConfig::XML_PATH_ON_ACCOUNT],
            'address form' => ['customer_address_form', FrontendConfig::XML_PATH_ON_ACCOUNT],
            'address new' => ['customer_address_new', FrontendConfig::XML_PATH_ON_ACCOUNT],
            'registration' => ['customer_account_create', FrontendConfig::XML_PATH_ON_REGISTRATION],
            'checkout' => ['checkout_index_index', FrontendConfig::XML_PATH_ON_CHECKOUT],
            'multishipping' => ['multishipping_checkout_addresses', FrontendConfig::XML_PATH_ON_CHECKOUT],
        ];
    }

    public function testAccountFlagDoesNotEnableCheckout(): void
    {
        $viewModel = $this->viewModel(
            [FrontendConfig::XML_PATH_ENABLED => true, FrontendConfig::XML_PATH_ON_ACCOUNT => true],
            [],
            'checkout_index_index'
        );

        $this->assertFalse($viewModel->isActiveOnPage());
    }

    public function testOtherPagesAreActiveWhenEnabled(): void
    {
        $viewModel = $this->viewModel([FrontendConfig::XML_PATH_ENABLED => true], [], 'catalog_product_view');

        $this->assertTrue($viewModel->isActiveOnPage());
    }

    public function testJsConfigUsesDefaultsForBlankOrInvalidValues(): void
    {
        $viewModel = $this->viewModel([], [
            FrontendConfig::XML_PATH_SUCCESS_FORMAT => '   ',
            FrontendConfig::XML_PATH_SUCCESS_COLOR => 'green',
            FrontendConfig::XML_PATH_ERROR_COLOR => '#12345',
        ]);

        $this->assertSame([
            'validationUrl' => 'https://shop.test/zipcodevalidation/validate/pincode',
            'showSuccess' => false,
            'successFormat' => 'Valid PIN code for {state}',
            'successColor' => '#007a33',
            'errorColor' => '#e02b27',
        ], $viewModel->getJsConfig());
    }

    public function testJsConfigAcceptsValidHexColorsAndCustomFormat(): void
    {
        $viewModel = $this->viewModel([FrontendConfig::XML_PATH_SHOW_SUCCESS => true], [
            FrontendConfig::XML_PATH_SUCCESS_FORMAT => ' Ships to {state} ',
            FrontendConfig::XML_PATH_SUCCESS_COLOR => '#0F0',
            FrontendConfig::XML_PATH_ERROR_COLOR => ' #AA000080 ',
        ]);

        $config = $viewModel->getJsConfig();

        $this->assertTrue($config['showSuccess']);
        $this->assertSame('Ships to {state}', $config['successFormat']);
        $this->assertSame('#0F0', $config['successColor']);
        $this->assertSame('#AA000080', $config['errorColor']);
    }

    public function testCheckoutConfigRequiresBothFlags(): void
    {
        $this->assertFalse(
            $this->viewModel([FrontendConfig::XML_PATH_ON_CHECKOUT => true])->getCheckoutConfig()['enabled']
        );
        $this->assertFalse(
            $this->viewModel([FrontendConfig::XML_PATH_ENABLED => true])->getCheckoutConfig()['enabled']
        );

        $config = $this->viewModel([
            FrontendConfig::XML_PATH_ENABLED => true,
            FrontendConfig::XML_PATH_ON_CHECKOUT => true,
        ])->getCheckoutConfig();
        $this->assertSame(
            ['enabled' => true, 'showSuccess' => false, 'successFormat' => 'Valid PIN code for {state}'],
            $config
        );
    }

    public function testConfigProviderWrapsCheckoutConfig(): void
    {
        $viewModel = $this->createStub(FrontendConfig::class);
        $viewModel->method('getCheckoutConfig')->willReturn(['enabled' => true]);

        $this->assertSame(
            ['panthZipcodeValidation' => ['enabled' => true]],
            (new ConfigProvider($viewModel))->getConfig()
        );
    }
}
