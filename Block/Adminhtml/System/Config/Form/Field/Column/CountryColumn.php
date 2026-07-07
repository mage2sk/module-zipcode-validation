<?php
namespace Panth\ZipcodeValidation\Block\Adminhtml\System\Config\Form\Field\Column;

use Magento\Framework\View\Element\Html\Select;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;

class CountryColumn extends Select
{
    protected $countryCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        CountryCollectionFactory $countryCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->countryCollectionFactory = $countryCollectionFactory;
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    public function setInputId($value)
    {
        return $this->setId($value);
    }

    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }

        return parent::_toHtml();
    }

    protected function getSourceOptions(): array
    {
        $options = [['value' => '', 'label' => __('-- Please Select --')]];

        $collection = $this->countryCollectionFactory->create();
        foreach ($collection as $country) {
            $options[] = [
                'value' => $country->getId(),
                'label' => $country->getName()
            ];
        }

        return $options;
    }
}
