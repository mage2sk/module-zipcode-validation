<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Ui\DataProvider;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange\CollectionFactory;

class RangeDataProvider extends AbstractDataProvider
{
    private RequestInterface $request;
    private array $loadedData = [];

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        foreach ($items as $item) {
            $this->loadedData[$item->getId()] = $item->getData();
        }

        // For new items, return empty
        $id = $this->request->getParam('id');
        if ($id && !isset($this->loadedData[$id])) {
            $this->loadedData[$id] = [];
        }

        return $this->loadedData;
    }
}
