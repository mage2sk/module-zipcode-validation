<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Model;

use Magento\Framework\Model\AbstractModel;

class ZipcodeRange extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange::class);
    }
}
