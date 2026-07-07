<?php
namespace Panth\ZipcodeValidation\Controller\Adminhtml\Sample;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Serialize\Serializer\Json;

class Index extends Action
{
    protected $fileFactory;

    protected $serializer;

    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        Json $serializer
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->serializer = $serializer;
    }

    public function execute()
    {
        try {
            $sampleData = [
                [
                    'country_id' => 'IN',
                    'state_code' => 'MH',
                    'state_name' => 'Maharashtra',
                    'pincode_start' => '400001',
                    'pincode_end' => '445402'
                ],
                [
                    'country_id' => 'IN',
                    'state_code' => 'DL',
                    'state_name' => 'Delhi',
                    'pincode_start' => '110001',
                    'pincode_end' => '110097'
                ],
                [
                    'country_id' => 'IN',
                    'state_code' => 'KA',
                    'state_name' => 'Karnataka',
                    'pincode_start' => '560001',
                    'pincode_end' => '591346'
                ],
                [
                    'country_id' => 'US',
                    'state_code' => 'CA',
                    'state_name' => 'California',
                    'pincode_start' => '90001',
                    'pincode_end' => '96162'
                ],
                [
                    'country_id' => 'US',
                    'state_code' => 'NY',
                    'state_name' => 'New York',
                    'pincode_start' => '10001',
                    'pincode_end' => '14925'
                ],
                [
                    'country_id' => 'GB',
                    'state_code' => 'LND',
                    'state_name' => 'London',
                    'pincode_start' => 'E1',
                    'pincode_end' => 'WC2N'
                ]
            ];

            $jsonContent = $this->serializer->serialize($sampleData);
            $fileName = 'zipcode_validation_sample.json';

            return $this->fileFactory->create(
                $fileName,
                $jsonContent,
                DirectoryList::VAR_DIR,
                'application/json'
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error generating sample file: %1', $e->getMessage()));
            return $this->_redirect('adminhtml/system_config/edit/section/zipcode_validation');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Panth_ZipcodeValidation::config');
    }
}
