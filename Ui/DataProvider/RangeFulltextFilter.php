<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Ui\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterApplierInterface;

class RangeFulltextFilter implements FilterApplierInterface
{
    public const SEARCH_FIELDS = ['country_id', 'state_code', 'state_name', 'zip_start', 'zip_end'];

    public function apply(Collection $collection, Filter $filter)
    {
        $value = trim((string) $filter->getValue());
        if ($value === '' || !$collection instanceof AbstractDb) {
            return;
        }

        $like = '%' . addcslashes($value, '\\%_') . '%';
        $conditions = [];
        foreach (self::SEARCH_FIELDS as $field) {
            $conditions[] = ['like' => $like];
        }
        $collection->addFieldToFilter(self::SEARCH_FIELDS, $conditions);
    }
}
