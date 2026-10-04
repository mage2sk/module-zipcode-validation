<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Block\Adminhtml\Range\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    private Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getButtonData(): array
    {
        $id = (int) $this->context->getRequest()->getParam("id");
        if ($id <= 0) {
            return [];
        }
        $url = $this->context->getUrlBuilder()->getUrl("*/*/delete", ["id" => $id]);
        return [
            "label" => __("Delete"),
            "class" => "delete",
            "on_click" => sprintf(
                "deleteConfirm(%s, %s, {\"data\": {}})",
                json_encode((string) __("Are you sure you want to delete this range?"), JSON_HEX_APOS | JSON_HEX_QUOT),
                json_encode($url, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES)
            ),
            "sort_order" => 20,
        ];
    }
}
