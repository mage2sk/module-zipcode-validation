<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Controller\Validate;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Panth\ZipcodeValidation\Model\PincodeValidator;
use Panth\ZipcodeValidation\Model\RateLimiter;

class Pincode extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    private const MAX_PINCODE_LENGTH = 20;
    private const MAX_REGION_LENGTH = 20;

    private JsonFactory $resultJsonFactory;
    private PincodeValidator $validator;
    private RateLimiter $rateLimiter;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        PincodeValidator $validator,
        RateLimiter $rateLimiter
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->validator = $validator;
        $this->rateLimiter = $rateLimiter;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $result->setHeader('Cache-Control', 'max-age=0, must-revalidate, no-cache, no-store', true);
        $result->setHeader('Pragma', 'no-cache', true);

        if (!$this->validator->isEnabled()) {
            return $result->setData(['valid' => true, 'message' => '']);
        }

        if (!$this->rateLimiter->isAllowed('check')) {
            $result->setHttpResponseCode(429);
            return $result->setData([
                'valid' => true,
                'rate_limited' => true,
                'message' => (string) __('Too many requests. Please try again later.')
            ]);
        }

        $pincode = $this->getStringParam('pincode');
        $countryId = strtoupper($this->getStringParam('country_id'));
        $regionId = $this->getStringParam('region_id');

        if ($pincode === '') {
            return $result->setData(['valid' => true, 'message' => '']);
        }

        if (mb_strlen($pincode) > self::MAX_PINCODE_LENGTH) {
            return $result->setData(['valid' => false, 'message' => 'Please enter a valid postal/ZIP code.']);
        }

        if ($countryId === '') {
            $countryId = 'IN';
        }
        if (!preg_match('/^[A-Z]{2}$/', $countryId)) {
            return $result->setData(['valid' => false, 'message' => 'Please select a valid country.']);
        }

        if (mb_strlen($regionId) > self::MAX_REGION_LENGTH || !preg_match('/^[A-Za-z0-9 _-]*$/', $regionId)) {
            $regionId = '';
        }

        $validationResult = $this->validator->validate($pincode, $regionId !== '' ? $regionId : null, $countryId);
        return $result->setData($validationResult);
    }

    private function getStringParam(string $name): string
    {
        $value = $this->getRequest()->getParam($name, '');
        if (!is_scalar($value)) {
            return '';
        }
        return trim((string) $value);
    }
}
