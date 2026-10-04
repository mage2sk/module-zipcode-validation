<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Ui\DataProvider;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange\CollectionFactory;

class RangeDataProvider extends AbstractDataProvider
{
    public const PERSISTOR_KEY = 'panth_zipcode_range';

    private RequestInterface $request;
    private DataPersistorInterface $dataPersistor;
    private array $loadedData = [];

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        $id = $this->request->getParam('id');
        if ($id) {
            $this->collection->addFieldToFilter('range_id', (int) $id);
        }

        $items = $this->collection->getItems();
        foreach ($items as $item) {
            $this->loadedData[$item->getId()] = $item->getData();
        }

        if ($id && !isset($this->loadedData[$id])) {
            $this->loadedData[$id] = [];
        }

        $persisted = $this->dataPersistor->get(self::PERSISTOR_KEY);
        if (is_array($persisted) && $persisted) {
            $persistedId = (int) ($persisted['range_id'] ?? 0);
            if ($persistedId === (int) $id) {
                $key = $persistedId ?: '';
                $this->loadedData[$key] = array_merge($this->loadedData[$key] ?? [], $persisted);
            }
            $this->dataPersistor->clear(self::PERSISTOR_KEY);
        }

        return $this->loadedData;
    }
}
