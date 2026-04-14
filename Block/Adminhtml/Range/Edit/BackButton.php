<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Block\Adminhtml\Range\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton implements ButtonProviderInterface
{
    private Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getButtonData(): array
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->context->getUrlBuilder()->getUrl('*/*/index')),
            'class' => 'back',
            'sort_order' => 10,
        ];
    }
}
