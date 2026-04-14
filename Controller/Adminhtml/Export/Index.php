<?php
/**
 * Panth_ZipcodeValidation
 *
 * @category  Panth
 * @package   Panth_ZipcodeValidation
 * @author    Panth
 * @copyright Copyright (c) 2025 Panth
 */

namespace Panth\ZipcodeValidation\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Filesystem\DirectoryList;

class Index extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Panth_ZipcodeValidation::config';

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * Constructor
     *
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $serializer
     */
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

    /**
     * Execute export
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        try {
            // Get current configuration
            $configData = $this->scopeConfig->getValue(
                'zipcode_validation/pincode_ranges/custom_ranges',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $data = [];
            if ($configData) {
                $data = $this->serializer->unserialize($configData);
            }

            // Create JSON content
            $jsonContent = $this->serializer->serialize($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            // Generate filename with timestamp
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
