<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;

class OrderAddressValidator
{
    private PincodeValidator $validator;

    public function __construct(PincodeValidator $validator)
    {
        $this->validator = $validator;
    }

    public function assertValid(?AddressInterface $address, ?int $storeId = null): void
    {
        if ($address === null
            || !$this->validator->isEnabled($storeId)
            || !$this->validator->isEnforceOnOrder($storeId)
        ) {
            return;
        }

        $countryId = strtoupper(trim((string) $address->getCountryId()));
        if ($countryId === '' || !$this->validator->hasRanges($countryId)) {
            return;
        }

        $regionId = (int) $address->getRegionId();
        $result = $this->validator->validate(
            trim((string) $address->getPostcode()),
            $regionId > 0 ? (string) $regionId : null,
            $countryId
        );

        if (empty($result['valid'])) {
            throw new LocalizedException(
                __('The shipping address postcode cannot be accepted: %1', (string) ($result['message'] ?? ''))
            );
        }
    }
}
