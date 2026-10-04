<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Controller\Adminhtml\Range;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange\CollectionFactory;

class Export extends Action
{
    const ADMIN_RESOURCE = 'Panth_ZipcodeValidation::config';

    private FileFactory $fileFactory;
    private CollectionFactory $collectionFactory;

    public function __construct(Context $context, FileFactory $fileFactory, CollectionFactory $collectionFactory)
    {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $collection = $this->collectionFactory->create();
        $data = [];
        foreach ($collection as $item) {
            $data[] = [
                'country_id' => $item->getData('country_id'),
                'state_code' => $item->getData('state_code'),
                'state_name' => $item->getData('state_name'),
                'zip_start' => $item->getData('zip_start'),
                'zip_end' => $item->getData('zip_end'),
                'is_active' => (int) $item->getData('is_active'),
            ];
        }

        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'zipcode_ranges_' . date('Y-m-d_H-i-s') . '.json';

        return $this->fileFactory->create(
            $fileName,
            ['type' => 'string', 'value' => $content, 'rm' => true],
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
            'application/json'
        );
    }
}
