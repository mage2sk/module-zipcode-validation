<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Model;

use Magento\Directory\Model\RegionFactory;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange\CollectionFactory;

class PincodeValidator
{
    private RegionFactory $regionFactory;
    private CollectionFactory $collectionFactory;
    private ?array $rangeCache = null;

    public function __construct(
        RegionFactory $regionFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->regionFactory = $regionFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Load active ranges from DB table
     */
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

    /**
     * Get region code from region ID
     */
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
            // ignore
        }
        return null;
    }

    /**
     * Validate a ZIP/PIN code
     */
    public function validate(string $pincode, ?string $regionId = null, string $countryId = 'IN'): array
    {
        $pincode = preg_replace('/[^0-9a-zA-Z]/', '', $pincode);

        if (empty($pincode)) {
            return ['valid' => false, 'message' => 'Please enter your postal/ZIP code.'];
        }

        // For India: must be 6 digits, first digit 1-9
        if ($countryId === 'IN' && !preg_match('/^[1-9][0-9]{5}$/', $pincode)) {
            return ['valid' => false, 'message' => 'Indian PIN codes must be 6 digits (e.g. 110001).'];
        }

        $ranges = $this->loadRanges($countryId);

        if (empty($ranges)) {
            // No ranges configured for this country — skip validation
            return ['valid' => true, 'message' => '', 'state' => ''];
        }

        // Find matching ranges
        $matchingStates = [];
        foreach ($ranges as $data) {
            if ($this->isInRange($pincode, $data['pincode_start'], $data['pincode_end'])) {
                $matchingStates[] = $data;
            }
        }

        if (empty($matchingStates)) {
            return ['valid' => false, 'message' => 'We couldn\'t verify this postal code. Please double-check and try again.'];
        }

        // Validate against selected state/region if provided
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

    /**
     * Check if a code falls within a range (numeric or string comparison)
     */
    private function isInRange(string $code, string $start, string $end): bool
    {
        if (is_numeric($code) && is_numeric($start) && is_numeric($end)) {
            return (int) $code >= (int) $start && (int) $code <= (int) $end;
        }
        return strcasecmp($code, $start) >= 0 && strcasecmp($code, $end) <= 0;
    }

    /**
     * Get state by pincode
     */
    public function getStateByPincode(string $pincode, string $countryId = 'IN'): ?string
    {
        $result = $this->validate($pincode, null, $countryId);
        return $result['valid'] ? ($result['state'] ?? null) : null;
    }
}
