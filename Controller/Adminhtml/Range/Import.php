<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Controller\Adminhtml\Range;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Panth\ZipcodeValidation\Model\ZipcodeRangeFactory;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange as ZipcodeRangeResource;

class Import extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Panth_ZipcodeValidation::config';

    private JsonFactory $jsonFactory;
    private ZipcodeRangeFactory $rangeFactory;
    private ZipcodeRangeResource $rangeResource;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        ZipcodeRangeFactory $rangeFactory,
        ZipcodeRangeResource $rangeResource
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->rangeFactory = $rangeFactory;
        $this->rangeResource = $rangeResource;
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        try {
            $jsonData = $this->getRequest()->getContent();
            $ranges = json_decode($jsonData, true);

            if (!is_array($ranges)) {
                return $result->setData(['success' => false, 'message' => 'Invalid JSON format.']);
            }

            $imported = 0;
            $errors = [];
            $rowNumber = 0;
            foreach ($ranges as $row) {
                $rowNumber++;
                if (!is_array($row)
                    || empty($row['country_id']) || empty($row['state_name'])
                    || empty($row['zip_start']) || empty($row['zip_end'])
                ) {
                    $errors[] = 'Row ' . $rowNumber . ': Missing required fields.';
                    continue;
                }
                if (!is_string($row['country_id']) || !preg_match('/^[A-Za-z]{2}$/', $row['country_id'])
                    || !is_scalar($row['state_name']) || mb_strlen((string) $row['state_name']) > 255
                    || !is_scalar($row['state_code'] ?? '') || mb_strlen((string) ($row['state_code'] ?? '')) > 10
                    || !is_scalar($row['zip_start']) || mb_strlen((string) $row['zip_start']) > 20
                    || !is_scalar($row['zip_end']) || mb_strlen((string) $row['zip_end']) > 20
                ) {
                    $errors[] = 'Row ' . $rowNumber . ': Invalid field value or length.';
                    continue;
                }
                try {
                    $range = $this->rangeFactory->create();
                    $range->setData([
                        'country_id' => strtoupper($row['country_id']),
                        'state_code' => (string) ($row['state_code'] ?? ''),
                        'state_name' => (string) $row['state_name'],
                        'zip_start' => (string) $row['zip_start'],
                        'zip_end' => (string) $row['zip_end'],
                        'is_active' => isset($row['is_active']) && empty($row['is_active']) ? 0 : 1,
                    ]);
                    $this->rangeResource->save($range);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = 'Row ' . $rowNumber . ': ' . $e->getMessage();
                }
            }

            $message = sprintf('Successfully imported %d range(s).', $imported);
            if (!empty($errors)) {
                $message .= ' Errors: ' . implode('; ', array_slice($errors, 0, 5));
            }
            return $result->setData(['success' => true, 'message' => $message, 'imported' => $imported]);
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
