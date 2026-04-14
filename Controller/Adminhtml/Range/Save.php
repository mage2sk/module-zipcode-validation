<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Controller\Adminhtml\Range;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Panth\ZipcodeValidation\Model\ZipcodeRangeFactory;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange as ZipcodeRangeResource;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Panth_ZipcodeValidation::config';

    private ZipcodeRangeFactory $rangeFactory;
    private ZipcodeRangeResource $rangeResource;

    public function __construct(
        Context $context,
        ZipcodeRangeFactory $rangeFactory,
        ZipcodeRangeResource $rangeResource
    ) {
        parent::__construct($context);
        $this->rangeFactory = $rangeFactory;
        $this->rangeResource = $rangeResource;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        try {
            $id = (int) ($data['range_id'] ?? 0);
            $range = $this->rangeFactory->create();
            if ($id) {
                $this->rangeResource->load($range, $id);
            }

            $range->setData('country_id', $data['country_id'] ?? '');
            $range->setData('state_code', $data['state_code'] ?? '');
            $range->setData('state_name', $data['state_name'] ?? '');
            $range->setData('zip_start', $data['zip_start'] ?? '');
            $range->setData('zip_end', $data['zip_end'] ?? '');
            $range->setData('is_active', $data['is_active'] ?? 1);

            $this->rangeResource->save($range);
            $this->messageManager->addSuccessMessage(__('Range has been saved.'));

            if ($this->getRequest()->getParam('back') === 'edit') {
                return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['id' => $range->getId()]);
            }
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['id' => $id]);
        }
    }
}
