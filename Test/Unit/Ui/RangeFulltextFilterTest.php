<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Test\Unit\Ui;

use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use Panth\ZipcodeValidation\Ui\DataProvider\RangeFulltextFilter;
use PHPUnit\Framework\TestCase;

class RangeFulltextFilterTest extends TestCase
{
    private function filter(string $value): Filter
    {
        $filter = $this->createStub(Filter::class);
        $filter->method('getValue')->willReturn($value);
        return $filter;
    }

    public function testKeywordSearchesEveryTextColumnWithLike(): void
    {
        $collection = $this->createMock(AbstractDb::class);
        $collection->expects($this->once())->method('addFieldToFilter')->with(
            ['country_id', 'state_code', 'state_name', 'zip_start', 'zip_end'],
            array_fill(0, 5, ['like' => '%Ottawa%'])
        );

        (new RangeFulltextFilter())->apply($collection, $this->filter('  Ottawa '));
    }

    public function testLikeWildcardsInTheKeywordAreEscaped(): void
    {
        $collection = $this->createMock(AbstractDb::class);
        $collection->expects($this->once())->method('addFieldToFilter')->with(
            $this->anything(),
            array_fill(0, 5, ['like' => '%50\\%\\_a\\\\b%'])
        );

        (new RangeFulltextFilter())->apply($collection, $this->filter('50%_a\\b'));
    }

    public function testEmptyKeywordAddsNoCondition(): void
    {
        $collection = $this->createMock(AbstractDb::class);
        $collection->expects($this->never())->method('addFieldToFilter');

        (new RangeFulltextFilter())->apply($collection, $this->filter('   '));
    }

    public function testNonDatabaseCollectionIsLeftAlone(): void
    {
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->never())->method('addFieldToFilter');

        (new RangeFulltextFilter())->apply($collection, $this->filter('W1'));
    }
}
