<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Test\Unit\Ui;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Panth\ZipcodeValidation\Block\Adminhtml\Range\Edit\BackButton;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange\Collection;
use Panth\ZipcodeValidation\Model\ZipcodeRange;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange\CollectionFactory;
use Panth\ZipcodeValidation\Ui\Component\Listing\Column\RangeActions;
use Panth\ZipcodeValidation\Ui\DataProvider\RangeDataProvider;
use PHPUnit\Framework\TestCase;

class RangeUiTest extends TestCase
{
    private int $getItemsCalls = 0;

    private function provider(array $rows, $requestId): RangeDataProvider
    {
        $items = array_map(static function ($row) {
            $item = new class extends ZipcodeRange {
                public function __construct()
                {
                }
            };
            $item->setIdFieldName('range_id');
            return $item->setData($row);
        }, $rows);
        $collection = $this->createStub(Collection::class);
        $collection->method('getItems')->willReturnCallback(function () use ($items) {
            $this->getItemsCalls++;
            return $items;
        });
        $factory = $this->createStub(CollectionFactory::class);
        $factory->method('create')->willReturn($collection);

        $request = $this->createStub(RequestInterface::class);
        $request->method('getParam')->willReturn($requestId);

        return new RangeDataProvider('ds', 'range_id', 'id', $factory, $request);
    }

    public function testDataIsKeyedByIdAndCached(): void
    {
        $provider = $this->provider([
            ['range_id' => 1, 'state_name' => 'Delhi'],
            ['range_id' => 2, 'state_name' => 'Goa'],
        ], '1');

        $data = $provider->getData();
        $provider->getData();

        $this->assertSame(['range_id' => 2, 'state_name' => 'Goa'], $data[2]);
        $this->assertCount(2, $data);
        $this->assertSame(1, $this->getItemsCalls);
    }

    public function testMissingRequestedIdGetsEmptyEntry(): void
    {
        $data = $this->provider([['range_id' => 1]], '42')->getData();

        $this->assertSame([], $data[42]);
    }

    public function testNoRequestIdAndNoItemsReturnsEmptyArray(): void
    {
        $this->assertSame([], $this->provider([], null)->getData());
    }

    public function testActionsColumnAddsEditAndDeleteLinks(): void
    {
        $url = $this->createStub(UrlInterface::class);
        $url->method('getUrl')->willReturnCallback(
            static fn($route, $params) => $route . '/id/' . $params['id']
        );
        $column = new RangeActions(
            $this->createStub(ContextInterface::class),
            $this->createStub(UiComponentFactory::class),
            $url,
            [],
            ['name' => 'actions']
        );

        $result = $column->prepareDataSource(['data' => ['items' => [
            ['range_id' => 7],
            ['state_name' => 'no id'],
        ]]]);

        $actions = $result['data']['items'][0]['actions'];
        $this->assertSame('zipcodevalidation/range/edit/id/7', $actions['edit']['href']);
        $this->assertSame('zipcodevalidation/range/delete/id/7', $actions['delete']['href']);
        $this->assertTrue($actions['delete']['post']);
        $this->assertSame('Delete Range', (string) $actions['delete']['confirm']['title']);
        $this->assertArrayNotHasKey('actions', $result['data']['items'][1]);
    }

    public function testActionsColumnLeavesSourceWithoutItemsUntouched(): void
    {
        $column = new RangeActions(
            $this->createStub(ContextInterface::class),
            $this->createStub(UiComponentFactory::class),
            $this->createStub(UrlInterface::class)
        );

        $this->assertSame(['data' => ['totalRecords' => 0]], $column->prepareDataSource(['data' => ['totalRecords' => 0]]));
    }

    public function testBackButtonPointsToGrid(): void
    {
        $url = $this->createStub(UrlInterface::class);
        $url->method('getUrl')->willReturn('https://admin.test/ranges/');
        $context = $this->createStub(Context::class);
        $context->method('getUrlBuilder')->willReturn($url);

        $data = (new BackButton($context))->getButtonData();

        $this->assertSame("location.href = 'https://admin.test/ranges/';", $data['on_click']);
        $this->assertSame('back', $data['class']);
        $this->assertSame('Back', (string) $data['label']);
    }
}
