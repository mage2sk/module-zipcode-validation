<?php
/**
 * Panth_ZipcodeValidation
 *
 * @category  Panth
 * @package   Panth_ZipcodeValidation
 * @author    Panth
 * @copyright Copyright (c) 2025 Panth
 */

namespace Panth\ZipcodeValidation\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;

class Country implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $countryCollectionFactory;

    /**
     * Constructor
     *
     * @param CollectionFactory $countryCollectionFactory
     */
    public function __construct(
        CollectionFactory $countryCollectionFactory
    ) {
        $this->countryCollectionFactory = $countryCollectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
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
