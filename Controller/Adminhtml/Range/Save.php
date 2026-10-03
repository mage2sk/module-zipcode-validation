<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Controller\Adminhtml\Range;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Panth\ZipcodeValidation\Model\ZipcodeRangeFactory;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange as ZipcodeRangeResource;

class Save extends Action implements HttpPostActionInterface
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

        $id = (int) ($data['range_id'] ?? 0);
        try {
            $range = $this->rangeFactory->create();
            if ($id) {
                $this->rangeResource->load($range, $id);
                if (!$range->getId()) {
                    $this->messageManager->addErrorMessage(__('This range no longer exists.'));
                    return $this->resultRedirectFactory->create()->setPath('*/*/');
                }
            }

            $values = [
                'country_id' => strtoupper(trim((string) ($data['country_id'] ?? ''))),
                'state_code' => trim((string) ($data['state_code'] ?? '')),
                'state_name' => trim((string) ($data['state_name'] ?? '')),
                'zip_start' => trim((string) ($data['zip_start'] ?? '')),
                'zip_end' => trim((string) ($data['zip_end'] ?? '')),
                'is_active' => isset($data['is_active']) && empty($data['is_active']) ? 0 : 1,
            ];
            $errors = $this->validate($values);
            if ($errors) {
                foreach ($errors as $error) {
                    $this->messageManager->addErrorMessage($error);
                }
                return $this->resultRedirectFactory->create()->setPath(
                    $id ? '*/*/edit' : '*/*/new',
                    $id ? ['id' => $id] : []
                );
            }

            foreach ($values as $key => $value) {
                $range->setData($key, $value);
            }

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

    private function validate(array $values): array
    {
        $errors = [];
        if (!preg_match('/^[A-Z]{2}$/', $values['country_id'])) {
            $errors[] = __('Please select a valid country.');
        }
        if ($values['state_name'] === '' || mb_strlen($values['state_name']) > 255) {
            $errors[] = __('State/Region Name is required and can have at most 255 characters.');
        }
        if (mb_strlen($values['state_code']) > 10) {
            $errors[] = __('State/Region Code can have at most 10 characters.');
        }
        foreach (['zip_start' => __('ZIP/PIN Start'), 'zip_end' => __('ZIP/PIN End')] as $key => $label) {
            if ($values[$key] === '' || mb_strlen($values[$key]) > 20
                || preg_replace('/[^0-9a-zA-Z]/', '', $values[$key]) === ''
            ) {
                $errors[] = __('%1 is required, can have at most 20 characters and must contain letters or digits.', $label);
            }
        }
        return $errors;
    }
}
