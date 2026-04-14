<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class RangeActions extends Column
{
    private UrlInterface $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['range_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl('zipcodevalidation/range/edit', ['id' => $item['range_id']]),
                            'label' => __('Edit'),
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl('zipcodevalidation/range/delete', ['id' => $item['range_id']]),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete Range'),
                                'message' => __('Are you sure you want to delete this range?'),
                            ],
                        ],
                    ];
                }
            }
        }
        return $dataSource;
    }
}
