<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Controller\Adminhtml\Range;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Panth\ZipcodeValidation\Model\ZipcodeRangeFactory;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange as ZipcodeRangeResource;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Panth_ZipcodeValidation::config';

    private ZipcodeRangeFactory $rangeFactory;
    private ZipcodeRangeResource $rangeResource;

    public function __construct(Context $context, ZipcodeRangeFactory $rangeFactory, ZipcodeRangeResource $rangeResource)
    {
        parent::__construct($context);
        $this->rangeFactory = $rangeFactory;
        $this->rangeResource = $rangeResource;
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');
        try {
            $range = $this->rangeFactory->create();
            $this->rangeResource->load($range, $id);
            if ($range->getId()) {
                $this->rangeResource->delete($range);
                $this->messageManager->addSuccessMessage(__('Range has been deleted.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
