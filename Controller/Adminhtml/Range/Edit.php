<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Controller\Adminhtml\Range;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Panth\ZipcodeValidation\Model\ZipcodeRangeFactory;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange as ZipcodeRangeResource;

class Edit extends Action
{
    const ADMIN_RESOURCE = 'Panth_ZipcodeValidation::config';

    private PageFactory $resultPageFactory;
    private ZipcodeRangeFactory $rangeFactory;
    private ZipcodeRangeResource $rangeResource;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ZipcodeRangeFactory $rangeFactory,
        ZipcodeRangeResource $rangeResource
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->rangeFactory = $rangeFactory;
        $this->rangeResource = $rangeResource;
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');
        $range = $this->rangeFactory->create();

        if ($id) {
            $this->rangeResource->load($range, $id);
            if (!$range->getId()) {
                $this->messageManager->addErrorMessage(__('This range no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Panth_ZipcodeValidation::manage_ranges');
        $resultPage->getConfig()->getTitle()->prepend($id ? __('Edit Range #%1', $id) : __('New Range'));
        return $resultPage;
    }
}
