<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Controller\Validate;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Panth\ZipcodeValidation\Model\PincodeValidator;

class Pincode extends Action
{
    private JsonFactory $resultJsonFactory;
    private PincodeValidator $validator;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        PincodeValidator $validator
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->validator = $validator;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $pincode = $this->getRequest()->getParam('pincode', '');
        $countryId = $this->getRequest()->getParam('country_id', '');
        $regionId = $this->getRequest()->getParam('region_id', '');

        if (empty($pincode)) {
            return $result->setData(['valid' => true, 'message' => '']);
        }

        $validationResult = $this->validator->validate($pincode, $regionId ?: null, $countryId ?: 'IN');
        return $result->setData($validationResult);
    }
}
