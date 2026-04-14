<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Controller\Adminhtml\Range;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Panth\ZipcodeValidation\Model\ZipcodeRangeFactory;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange as ZipcodeRangeResource;

class Import extends Action
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
            foreach ($ranges as $index => $row) {
                if (empty($row['country_id']) || empty($row['state_name']) || empty($row['zip_start']) || empty($row['zip_end'])) {
                    $errors[] = 'Row ' . ($index + 1) . ': Missing required fields.';
                    continue;
                }
                try {
                    $range = $this->rangeFactory->create();
                    $range->setData([
                        'country_id' => $row['country_id'],
                        'state_code' => $row['state_code'] ?? '',
                        'state_name' => $row['state_name'],
                        'zip_start' => $row['zip_start'],
                        'zip_end' => $row['zip_end'],
                        'is_active' => $row['is_active'] ?? 1,
                    ]);
                    $this->rangeResource->save($range);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = 'Row ' . ($index + 1) . ': ' . $e->getMessage();
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
