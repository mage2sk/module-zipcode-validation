<?php
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
    protected $serializer;

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

    public function beforeSave()
    {
        $value = $this->getValue();

        if (is_array($value)) {
            $value = array_filter($value, function($row) {
                return !empty($row['country_id']) ||
                       !empty($row['state_code']) ||
                       !empty($row['pincode_start']) ||
                       !empty($row['pincode_end']);
            });

            $value = array_values($value);

            $this->setValue($this->serializer->serialize($value));
        }

        return parent::beforeSave();
    }

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
