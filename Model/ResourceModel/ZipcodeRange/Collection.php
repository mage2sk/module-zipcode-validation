<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Panth\ZipcodeValidation\Model\ZipcodeRange;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange as ZipcodeRangeResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'range_id';

    protected function _construct()
    {
        $this->_init(ZipcodeRange::class, ZipcodeRangeResource::class);
    }
}
