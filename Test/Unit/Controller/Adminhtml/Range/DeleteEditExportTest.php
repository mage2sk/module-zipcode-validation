<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Test\Unit\Controller\Adminhtml\Range;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\DataObject;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Panth\ZipcodeValidation\Controller\Adminhtml\Range\Delete;
use Panth\ZipcodeValidation\Controller\Adminhtml\Range\Edit;
use Panth\ZipcodeValidation\Controller\Adminhtml\Range\Export;
use Panth\ZipcodeValidation\Controller\Adminhtml\Range\MassDelete;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange\Collection;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange\CollectionFactory;

class DeleteEditExportTest extends ControllerTestCase
{
    private function collectionFactory(array $rows): CollectionFactory
    {
        $collection = $this->createStub(Collection::class);
        $collection->method('getIterator')->willReturnCallback(
            static fn() => new \ArrayIterator(array_map(static fn($r) => new DataObject($r), $rows))
        );
        $factory = $this->createStub(CollectionFactory::class);
        $factory->method('create')->willReturn($collection);
        return $factory;
    }

    public function testDeleteRemovesExistingRange(): void
    {
        $this->params = ['id' => '3'];

        (new Delete($this->context(), $this->rangeFactory(), $this->resource([3 => ['state_name' => 'X']])))
            ->execute();

        $this->assertCount(1, $this->deleted);
        $this->assertSame(3, (int) $this->deleted[0]->getId());
        $this->assertSame(['Range has been deleted.'], $this->messagesOf('success'));
        $this->assertSame(['*/*/', []], $this->redirect);
    }

    public function testDeleteOfUnknownRangeIsSilent(): void
    {
        $this->params = ['id' => '8'];

        (new Delete($this->context(), $this->rangeFactory(), $this->resource()))->execute();

        $this->assertSame([], $this->deleted);
        $this->assertSame([], $this->messages);
        $this->assertSame(['*/*/', []], $this->redirect);
    }

    public function testMassDeleteDeletesFilteredItemsAndCounts(): void
    {
        $items = [$this->rangeFactory()->create()->setId(1), $this->rangeFactory()->create()->setId(2)];
        $collection = $this->createStub(Collection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator($items));
        $filter = $this->createStub(Filter::class);
        $filter->method('getCollection')->willReturn($collection);

        (new MassDelete($this->context(), $filter, $this->collectionFactory([]), $this->resource()))
            ->execute();

        $this->assertSame($items, $this->deleted);
        $this->assertSame(['A total of 2 range(s) have been deleted.'], $this->messagesOf('success'));
        $this->assertSame(['*/*/', []], $this->redirect);
    }

    public function testMassDeleteReportsFilterFailure(): void
    {
        $filter = $this->createStub(Filter::class);
        $filter->method('getCollection')->willThrowException(new \RuntimeException('No items selected'));

        (new MassDelete($this->context(), $filter, $this->collectionFactory([]), $this->resource()))
            ->execute();

        $this->assertSame(['No items selected'], $this->messagesOf('error'));
        $this->assertSame(['*/*/', []], $this->redirect);
    }

    private function pageFactory(array &$titles, ?Page &$page = null): PageFactory
    {
        $title = $this->createStub(Title::class);
        $title->method('prepend')->willReturnCallback(function ($t) use (&$titles) {
            $titles[] = (string) $t;
        });
        $config = $this->createStub(Config::class);
        $config->method('getTitle')->willReturn($title);
        $page = $this->createStub(Page::class);
        $page->method('getConfig')->willReturn($config);
        $factory = $this->createStub(PageFactory::class);
        $factory->method('create')->willReturn($page);
        return $factory;
    }

    public function testEditNewRangeShowsNewTitle(): void
    {
        $titles = [];
        $page = null;
        $factory = $this->pageFactory($titles, $page);

        $result = (new Edit($this->context(), $factory, $this->rangeFactory(), $this->resource()))->execute();

        $this->assertSame($page, $result);
        $this->assertSame(['New Range'], $titles);
    }

    public function testEditExistingRangeShowsIdInTitle(): void
    {
        $this->params = ['id' => '5'];
        $titles = [];

        (new Edit($this->context(), $this->pageFactory($titles), $this->rangeFactory(), $this->resource([5 => []])))
            ->execute();

        $this->assertSame(['Edit Range #5'], $titles);
    }

    public function testEditMissingRangeRedirectsWithError(): void
    {
        $this->params = ['id' => '6'];
        $titles = [];

        (new Edit($this->context(), $this->pageFactory($titles), $this->rangeFactory(), $this->resource()))
            ->execute();

        $this->assertSame([], $titles);
        $this->assertSame(['This range no longer exists.'], $this->messagesOf('error'));
        $this->assertSame(['*/*/', []], $this->redirect);
    }

    public function testExportWritesJsonFileOfAllRanges(): void
    {
        $captured = [];
        $response = $this->createStub(ResponseInterface::class);
        $fileFactory = $this->createStub(FileFactory::class);
        $fileFactory->method('create')->willReturnCallback(
            function (...$args) use (&$captured, $response) {
                $captured = $args;
                return $response;
            }
        );

        $result = (new Export($this->context(), $fileFactory, $this->collectionFactory([
            ['country_id' => 'IN', 'state_code' => 'DL', 'state_name' => 'Delhi',
                'zip_start' => '110001', 'zip_end' => '110096', 'is_active' => '1', 'range_id' => 9],
        ])))->execute();

        $this->assertSame($response, $result);
        $this->assertMatchesRegularExpression('/^zipcode_ranges_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.json$/', $captured[0]);
        $this->assertSame('string', $captured[1]['type']);
        $this->assertTrue($captured[1]['rm']);
        $this->assertSame(DirectoryList::VAR_DIR, $captured[2]);
        $this->assertSame('application/json', $captured[3]);
        $this->assertSame([[
            'country_id' => 'IN', 'state_code' => 'DL', 'state_name' => 'Delhi',
            'zip_start' => '110001', 'zip_end' => '110096', 'is_active' => 1,
        ]], json_decode($captured[1]['value'], true));
    }
}
