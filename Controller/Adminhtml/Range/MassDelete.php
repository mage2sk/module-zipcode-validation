<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Controller\Adminhtml\Range;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange\CollectionFactory;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange as ZipcodeRangeResource;

class MassDelete extends Action
{
    const ADMIN_RESOURCE = 'Panth_ZipcodeValidation::config';

    private Filter $filter;
    private CollectionFactory $collectionFactory;
    private ZipcodeRangeResource $rangeResource;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ZipcodeRangeResource $rangeResource
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->rangeResource = $rangeResource;
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $count = 0;
            foreach ($collection as $item) {
                $this->rangeResource->delete($item);
                $count++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 range(s) have been deleted.', $count));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
