<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class FrontendConfig implements ArgumentInterface
{
    public const XML_PATH_ENABLED = 'zipcode_validation/general/enabled';
    public const XML_PATH_ON_CHECKOUT = 'zipcode_validation/general/validate_on_checkout';
    public const XML_PATH_ON_ACCOUNT = 'zipcode_validation/general/validate_on_account';
    public const XML_PATH_ON_REGISTRATION = 'zipcode_validation/general/validate_on_registration';
    public const XML_PATH_SHOW_SUCCESS = 'zipcode_validation/general/show_success_message';
    public const XML_PATH_SUCCESS_FORMAT = 'zipcode_validation/general/success_message_format';
    public const XML_PATH_SUCCESS_COLOR = 'zipcode_validation/display/success_color';
    public const XML_PATH_ERROR_COLOR = 'zipcode_validation/display/error_color';

    private const DEFAULT_SUCCESS_FORMAT = 'Valid PIN code for {state}';
    private const DEFAULT_SUCCESS_COLOR = '#007a33';
    private const DEFAULT_ERROR_COLOR = '#e02b27';

    private const ACCOUNT_ACTIONS = [
        'customer_address_form',
        'customer_address_new',
        'customer_address_edit',
    ];

    private const REGISTRATION_ACTIONS = [
        'customer_account_create',
    ];

    private ScopeConfigInterface $scopeConfig;
    private RequestInterface $request;
    private UrlInterface $urlBuilder;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request,
        UrlInterface $urlBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    public function isEnabled(): bool
    {
        return $this->isFlag(self::XML_PATH_ENABLED);
    }

    public function isActiveOnPage(): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $action = method_exists($this->request, 'getFullActionName')
            ? (string) $this->request->getFullActionName()
            : '';

        if (in_array($action, self::ACCOUNT_ACTIONS, true)) {
            return $this->isFlag(self::XML_PATH_ON_ACCOUNT);
        }
        if (in_array($action, self::REGISTRATION_ACTIONS, true)) {
            return $this->isFlag(self::XML_PATH_ON_REGISTRATION);
        }
        if (strpos($action, 'checkout_') === 0 || strpos($action, 'multishipping_') === 0) {
            return $this->isFlag(self::XML_PATH_ON_CHECKOUT);
        }

        return true;
    }

    public function getJsConfig(): array
    {
        return [
            'validationUrl' => $this->urlBuilder->getUrl('zipcodevalidation/validate/pincode'),
            'showSuccess' => $this->isFlag(self::XML_PATH_SHOW_SUCCESS),
            'successFormat' => $this->getSuccessFormat(),
            'successColor' => $this->getColor(self::XML_PATH_SUCCESS_COLOR, self::DEFAULT_SUCCESS_COLOR),
            'errorColor' => $this->getColor(self::XML_PATH_ERROR_COLOR, self::DEFAULT_ERROR_COLOR),
        ];
    }

    public function getCheckoutConfig(): array
    {
        return [
            'enabled' => $this->isEnabled() && $this->isFlag(self::XML_PATH_ON_CHECKOUT),
            'showSuccess' => $this->isFlag(self::XML_PATH_SHOW_SUCCESS),
            'successFormat' => $this->getSuccessFormat(),
        ];
    }

    private function getSuccessFormat(): string
    {
        $format = trim((string) $this->scopeConfig->getValue(self::XML_PATH_SUCCESS_FORMAT, ScopeInterface::SCOPE_STORE));
        return $format !== '' ? $format : self::DEFAULT_SUCCESS_FORMAT;
    }

    private function getColor(string $path, string $default): string
    {
        $color = trim((string) $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE));
        return preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $color) ? $color : $default;
    }

    private function isFlag(string $path): bool
    {
        return $this->scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORE);
    }
}
