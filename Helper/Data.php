<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Panth\ZipcodeValidation\Model\PincodeValidator;

class Data extends AbstractHelper
{
    private PincodeValidator $pincodeValidator;

    public function __construct(Context $context, PincodeValidator $pincodeValidator)
    {
        parent::__construct($context);
        $this->pincodeValidator = $pincodeValidator;
    }

    public function validateIndianPincode(string $pincode, ?string $regionId = null): array
    {
        return $this->pincodeValidator->validate($pincode, $regionId, 'IN');
    }

    public function validateZipcode(string $zipcode, ?string $regionId = null, string $countryId = 'IN'): array
    {
        return $this->pincodeValidator->validate($zipcode, $regionId, $countryId);
    }

    public function getStateByPincode(string $pincode, string $countryId = 'IN'): ?string
    {
        return $this->pincodeValidator->getStateByPincode($pincode, $countryId);
    }
}
