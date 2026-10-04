<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Model;

use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange\CollectionFactory;

class PincodeValidator
{
    private const XML_PATH_ENABLED = 'zipcode_validation/general/enabled';
    private const XML_PATH_ENFORCE_ON_ORDER = 'zipcode_validation/general/enforce_on_order';
    private const XML_PATH_ERROR_MESSAGE = 'zipcode_validation/general/error_message';
    private const DEFAULT_ERROR_MESSAGE = 'We couldn\'t verify this postal code. Please double-check and try again.';
    private const FORMATS = [
        'IN' => ['/^[1-9][0-9]{5}$/', 'Indian PIN codes must be 6 digits (e.g. 110001).'],
        'US' => ['/^[0-9]{5}([0-9]{4})?$/', 'US ZIP codes must be 5 digits or ZIP+4 (e.g. 10001 or 10001-1234).'],
        'CA' => ['/^[A-Z][0-9][A-Z][0-9][A-Z][0-9]$/', 'Canadian postal codes use the format A1A 1A1 (e.g. K1A 0B1).'],
        'GB' => ['/^([A-Z]{1,2}[0-9][A-Z0-9]?[0-9][A-Z]{2}|GIR0AA)$/', 'UK postcodes use a format like SW1A 1AA.'],
    ];

    private RegionFactory $regionFactory;
    private CollectionFactory $collectionFactory;
    private ?array $rangeCache = null;
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        RegionFactory $regionFactory,
        CollectionFactory $collectionFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->regionFactory = $regionFactory;
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isEnforceOnOrder(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENFORCE_ON_ORDER, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function hasRanges(string $countryId): bool
    {
        return !empty($this->loadRanges(strtoupper(trim($countryId))));
    }

    private function getErrorMessage(): string
    {
        $message = trim((string) $this->scopeConfig->getValue(self::XML_PATH_ERROR_MESSAGE, ScopeInterface::SCOPE_STORE));
        return $message !== '' ? $message : (string) __(self::DEFAULT_ERROR_MESSAGE);
    }

    private function loadRanges(string $countryId = ''): array
    {
        $cacheKey = $countryId ?: '__all__';
        if ($this->rangeCache === null) {
            $this->rangeCache = [];
        }
        if (isset($this->rangeCache[$cacheKey])) {
            return $this->rangeCache[$cacheKey];
        }

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        if ($countryId) {
            $collection->addFieldToFilter('country_id', $countryId);
        }

        $ranges = [];
        foreach ($collection as $item) {
            $ranges[] = [
                'country_id' => $item->getData('country_id'),
                'state_code' => $item->getData('state_code') ?? '',
                'state_name' => $item->getData('state_name'),
                'pincode_start' => $item->getData('zip_start'),
                'pincode_end' => $item->getData('zip_end'),
            ];
        }

        $this->rangeCache[$cacheKey] = $ranges;
        return $ranges;
    }

    private function getRegionCode($regionId): ?string
    {
        if (empty($regionId)) {
            return null;
        }
        if (!is_numeric($regionId)) {
            return strtoupper((string) $regionId);
        }
        try {
            $region = $this->regionFactory->create()->load($regionId);
            if ($region && $region->getId()) {
                return strtoupper($region->getCode());
            }
        } catch (\Exception $e) {
        }
        return null;
    }

    public function validate(string $pincode, ?string $regionId = null, string $countryId = 'IN'): array
    {
        $pincode = $this->normalize($pincode);
        $countryId = strtoupper(trim($countryId));

        if ($pincode === '') {
            return ['valid' => false, 'message' => (string) __('Please enter your postal/ZIP code.')];
        }

        $formatError = $this->getFormatError($pincode, $countryId);
        if ($formatError !== null) {
            return ['valid' => false, 'message' => $formatError];
        }

        $ranges = $this->loadRanges($countryId);

        if (empty($ranges)) {
            return ['valid' => true, 'message' => '', 'state' => ''];
        }

        $matchingStates = [];
        foreach ($ranges as $data) {
            if ($this->isInRange(
                $pincode,
                (string) $data['pincode_start'],
                (string) $data['pincode_end'],
                $countryId
            )) {
                $matchingStates[] = $data;
            }
        }

        if (empty($matchingStates)) {
            return ['valid' => false, 'message' => $this->getErrorMessage()];
        }

        if ($regionId !== null && $regionId !== '') {
            $regionCode = $this->getRegionCode($regionId);
            if ($regionCode) {
                $validForState = false;
                foreach ($matchingStates as $state) {
                    $stateCode = strtoupper(trim((string) $state['state_code']));
                    if ($stateCode === '' || $stateCode === $regionCode) {
                        $validForState = true;
                        break;
                    }
                }
                if (!$validForState) {
                    return [
                        'valid' => false,
                        'message' => (string) __(
                            'This postal code is associated with %1. Please check your state/region selection.',
                            $matchingStates[0]['state_name']
                        )
                    ];
                }
            }
        }

        return ['valid' => true, 'message' => '', 'state' => $matchingStates[0]['state_name']];
    }

    private function normalize(string $code): string
    {
        return strtoupper((string) preg_replace('/[^0-9a-zA-Z]/', '', $code));
    }

    private function getFormatError(string $pincode, string $countryId): ?string
    {
        if (!isset(self::FORMATS[$countryId]) || preg_match(self::FORMATS[$countryId][0], $pincode)) {
            return null;
        }
        return (string) __(self::FORMATS[$countryId][1]);
    }

    private function isInRange(string $code, string $start, string $end, string $countryId = ''): bool
    {
        $start = $this->normalize($start);
        $end = $this->normalize($end);
        if ($start === '' || $end === '') {
            return false;
        }
        $boundLength = max(strlen($start), strlen($end));
        if ($countryId === 'GB' && $boundLength <= 4 && $code !== 'GIR0AA'
            && preg_match(self::FORMATS['GB'][0], $code)
        ) {
            $code = substr($code, 0, -3);
        } elseif (strlen($code) > $boundLength) {
            $code = substr($code, 0, $boundLength);
        }
        return $this->compare($code, $start) >= 0 && $this->compare($code, $end) <= 0;
    }

    private function compare(string $left, string $right): int
    {
        if (ctype_digit($left) && ctype_digit($right)) {
            return (int) $left <=> (int) $right;
        }
        return strnatcasecmp($left, $right);
    }

    public function getStateByPincode(string $pincode, string $countryId = 'IN'): ?string
    {
        $result = $this->validate($pincode, null, $countryId);
        return $result['valid'] ? ($result['state'] ?? null) : null;
    }
}
