<?php
/**
 * Panth_ZipcodeValidation
 *
 * @category  Panth
 * @package   Panth_ZipcodeValidation
 * @author    Panth
 * @copyright Copyright (c) 2025 Panth
 */

namespace Panth\ZipcodeValidation\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;

class PincodeRanges extends Value
{
    /**
     * @var Json
     */
    protected $serializer;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param Json $serializer
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Json $serializer,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->serializer = $serializer;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Before save processing
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();

        if (is_array($value)) {
            // Remove empty rows
            $value = array_filter($value, function($row) {
                return !empty($row['country_id']) ||
                       !empty($row['state_code']) ||
                       !empty($row['pincode_start']) ||
                       !empty($row['pincode_end']);
            });

            // Reset array keys
            $value = array_values($value);

            // Serialize the array
            $this->setValue($this->serializer->serialize($value));
        }

        return parent::beforeSave();
    }

    /**
     * After load processing
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();

        if ($value && !is_array($value)) {
            try {
                $this->setValue($this->serializer->unserialize($value));
            } catch (\Exception $e) {
                $this->setValue([]);
            }
        }

        return parent::_afterLoad();
    }
}
