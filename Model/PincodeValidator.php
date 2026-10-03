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
        return $message !== '' ? $message : self::DEFAULT_ERROR_MESSAGE;
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
        $pincode = strtoupper((string) preg_replace('/[^0-9a-zA-Z]/', '', $pincode));
        $countryId = strtoupper(trim($countryId));

        if (empty($pincode)) {
            return ['valid' => false, 'message' => 'Please enter your postal/ZIP code.'];
        }

        if ($countryId === 'IN' && !preg_match('/^[1-9][0-9]{5}$/', $pincode)) {
            return ['valid' => false, 'message' => 'Indian PIN codes must be 6 digits (e.g. 110001).'];
        }

        $ranges = $this->loadRanges($countryId);

        if (empty($ranges)) {
            return ['valid' => true, 'message' => '', 'state' => ''];
        }

        $matchingStates = [];
        foreach ($ranges as $data) {
            if ($this->isInRange($pincode, $data['pincode_start'], $data['pincode_end'])) {
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
                    if (strtoupper($state['state_code']) === $regionCode) {
                        $validForState = true;
                        break;
                    }
                }
                if (!$validForState) {
                    $correctState = $matchingStates[0]['state_name'];
                    return [
                        'valid' => false,
                        'message' => "This postal code is associated with {$correctState}. Please check your state/region selection."
                    ];
                }
            }
        }

        return ['valid' => true, 'message' => '', 'state' => $matchingStates[0]['state_name']];
    }

    private function isInRange(string $code, string $start, string $end): bool
    {
        $start = (string) preg_replace('/[^0-9a-zA-Z]/', '', $start);
        $end = (string) preg_replace('/[^0-9a-zA-Z]/', '', $end);
        if ($start === '' || $end === '') {
            return false;
        }
        $boundLength = max(strlen($start), strlen($end));
        if (strlen($code) > $boundLength) {
            $code = substr($code, 0, $boundLength);
        }
        if (ctype_digit($code) && ctype_digit($start) && ctype_digit($end)) {
            return (int) $code >= (int) $start && (int) $code <= (int) $end;
        }
        return strcasecmp($code, $start) >= 0 && strcasecmp($code, $end) <= 0;
    }

    public function getStateByPincode(string $pincode, string $countryId = 'IN'): ?string
    {
        $result = $this->validate($pincode, null, $countryId);
        return $result['valid'] ? ($result['state'] ?? null) : null;
    }
}
