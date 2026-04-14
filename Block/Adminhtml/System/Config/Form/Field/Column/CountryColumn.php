<?php
/**
 * Panth_ZipcodeValidation
 *
 * @category  Panth
 * @package   Panth_ZipcodeValidation
 * @author    Panth
 * @copyright Copyright (c) 2025 Panth
 */

namespace Panth\ZipcodeValidation\Block\Adminhtml\System\Config\Form\Field\Column;

use Magento\Framework\View\Element\Html\Select;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;

class CountryColumn extends Select
{
    /**
     * @var CountryCollectionFactory
     */
    protected $countryCollectionFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param CountryCollectionFactory $countryCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        CountryCollectionFactory $countryCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->countryCollectionFactory = $countryCollectionFactory;
    }

    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }

        return parent::_toHtml();
    }

    /**
     * Get country options
     *
     * @return array
     */
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
