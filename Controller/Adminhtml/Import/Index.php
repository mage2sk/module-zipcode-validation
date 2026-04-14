<?php
/**
 * Panth_ZipcodeValidation
 *
 * @category  Panth
 * @package   Panth_ZipcodeValidation
 * @author    Panth
 * @copyright Copyright (c) 2025 Panth
 */

namespace Panth\ZipcodeValidation\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;

class Index extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Panth_ZipcodeValidation::config';

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var CountryCollectionFactory
     */
    protected $countryCollectionFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param WriterInterface $configWriter
     * @param Json $serializer
     * @param CountryCollectionFactory $countryCollectionFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        WriterInterface $configWriter,
        Json $serializer,
        CountryCollectionFactory $countryCollectionFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configWriter = $configWriter;
        $this->serializer = $serializer;
        $this->countryCollectionFactory = $countryCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute import
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        try {
            $jsonData = $this->getRequest()->getParam('json_data');

            if (empty($jsonData)) {
                return $result->setData([
                    'success' => false,
                    'message' => __('No data provided for import.')
                ]);
            }

            // Decode JSON
            $data = $this->serializer->unserialize($jsonData);

            if (!is_array($data)) {
                return $result->setData([
                    'success' => false,
                    'message' => __('Invalid JSON format.')
                ]);
            }

            // Validate data
            $validationResult = $this->validateImportData($data);

            if (!$validationResult['valid']) {
                return $result->setData([
                    'success' => false,
                    'message' => $validationResult['message'],
                    'errors' => $validationResult['errors']
                ]);
            }

            // Save to configuration
            $serializedData = $this->serializer->serialize($data);
            $this->configWriter->save(
                'zipcode_validation/pincode_ranges/custom_ranges',
                $serializedData
            );

            return $result->setData([
                'success' => true,
                'message' => __('Successfully imported %1 validation ranges.', count($data)),
                'count' => count($data)
            ]);

        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => __('Error during import: %1', $e->getMessage())
            ]);
        }
    }

    /**
     * Validate import data
     *
     * @param array $data
     * @return array
     */
    protected function validateImportData($data)
    {
        $errors = [];
        $validCountries = $this->getValidCountries();

        foreach ($data as $index => $range) {
            $rowNumber = $index + 1;

            // Validate required fields
            if (empty($range['country_id'])) {
                $errors[] = __('Row %1: Country ID is required.', $rowNumber);
            } elseif (!in_array($range['country_id'], $validCountries)) {
                $errors[] = __('Row %1: Invalid country code "%2".', $rowNumber, $range['country_id']);
            }

            if (empty($range['state_name'])) {
                $errors[] = __('Row %1: State/Region name is required.', $rowNumber);
            }

            if (!isset($range['pincode_start']) || $range['pincode_start'] === '') {
                $errors[] = __('Row %1: PIN/ZIP start is required.', $rowNumber);
            } elseif (!is_numeric($range['pincode_start'])) {
                $errors[] = __('Row %1: PIN/ZIP start must be numeric.', $rowNumber);
            }

            if (!isset($range['pincode_end']) || $range['pincode_end'] === '') {
                $errors[] = __('Row %1: PIN/ZIP end is required.', $rowNumber);
            } elseif (!is_numeric($range['pincode_end'])) {
                $errors[] = __('Row %1: PIN/ZIP end must be numeric.', $rowNumber);
            }

            // Validate range
            if (isset($range['pincode_start']) && isset($range['pincode_end'])) {
                if ((int)$range['pincode_start'] > (int)$range['pincode_end']) {
                    $errors[] = __('Row %1: PIN/ZIP start cannot be greater than end.', $rowNumber);
                }
            }
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'message' => __('Validation failed. Please fix the errors below:'),
                'errors' => $errors
            ];
        }

        return ['valid' => true];
    }

    /**
     * Get valid country codes
     *
     * @return array
     */
    protected function getValidCountries()
    {
        $countries = [];
        $collection = $this->countryCollectionFactory->create();

        foreach ($collection as $country) {
            $countries[] = $country->getId();
        }

        return $countries;
    }
}
