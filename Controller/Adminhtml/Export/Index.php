<?php
namespace Panth\ZipcodeValidation\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Filesystem\DirectoryList;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Panth_ZipcodeValidation::config';

    protected $fileFactory;

    protected $scopeConfig;

    protected $serializer;

    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        ScopeConfigInterface $scopeConfig,
        Json $serializer
    ) {
        $this->fileFactory = $fileFactory;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $configData = $this->scopeConfig->getValue(
                'zipcode_validation/pincode_ranges/custom_ranges',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $data = [];
            if ($configData) {
                $data = $this->serializer->unserialize($configData);
            }

            $jsonContent = $this->serializer->serialize($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            $filename = 'zipcode_validation_ranges_' . date('Y-m-d_H-i-s') . '.json';

            return $this->fileFactory->create(
                $filename,
                $jsonContent,
                DirectoryList::VAR_DIR,
                'application/json'
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Error during export: %1', $e->getMessage())
            );
            return $this->_redirect('adminhtml/system_config/edit/section/zipcode_validation');
        }
    }
}
