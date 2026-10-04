<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Test\Unit\Controller\Adminhtml\Range;

use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Panth\ZipcodeValidation\Controller\Adminhtml\Range\Import;

class ImportTest extends ControllerTestCase
{
    private ?array $json = null;

    private function dispatch(array $existing = [], ?\Throwable $saveError = null): array
    {
        $json = $this->createStub(Json::class);
        $json->method('setData')->willReturnCallback(function ($data) use ($json) {
            $this->json = $data;
            return $json;
        });
        $factory = $this->createStub(JsonFactory::class);
        $factory->method('create')->willReturn($json);

        (new Import($this->context(), $factory, $this->rangeFactory(), $this->resource($existing, $saveError)))
            ->execute();
        return $this->json;
    }

    public function testNonArrayPayloadIsRejected(): void
    {
        $this->content = 'not json';

        $this->assertSame(['success' => false, 'message' => 'Invalid JSON format.'], $this->dispatch());
        $this->assertSame([], $this->saved);
    }

    public function testValidRowsAreImportedWithDefaults(): void
    {
        $this->content = json_encode([
            ['country_id' => 'in', 'state_name' => 'Delhi', 'zip_start' => '110001', 'zip_end' => '110096'],
            ['country_id' => 'US', 'state_code' => 'NY', 'state_name' => 'New York',
                'zip_start' => 10001, 'zip_end' => 14925, 'is_active' => 0],
        ]);

        $result = $this->dispatch();

        $this->assertSame(
            ['success' => true, 'message' => 'Successfully imported 2 range(s).', 'imported' => 2],
            $result
        );
        $this->assertSame('IN', $this->saved[0]->getData('country_id'));
        $this->assertSame('', $this->saved[0]->getData('state_code'));
        $this->assertSame(1, $this->saved[0]->getData('is_active'));
        $this->assertSame('10001', $this->saved[1]->getData('zip_start'));
        $this->assertSame(0, $this->saved[1]->getData('is_active'));
    }

    public function testInvalidRowsAreReportedAndSkipped(): void
    {
        $this->content = json_encode([
            ['country_id' => 'IN', 'state_name' => 'Delhi', 'zip_start' => '110001'],
            'scalar row',
            ['country_id' => 'IND', 'state_name' => 'X', 'zip_start' => '1', 'zip_end' => '2'],
            ['country_id' => 'IN', 'state_name' => 'X', 'state_code' => 'ABCDEFGHIJK', 'zip_start' => '1', 'zip_end' => '2'],
            ['country_id' => 'IN', 'state_name' => 'X', 'zip_start' => ['1'], 'zip_end' => '2'],
            ['country_id' => 'IN', 'state_name' => 'Ok', 'zip_start' => '1', 'zip_end' => '2'],
        ]);

        $result = $this->dispatch();

        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['imported']);
        $this->assertStringStartsWith('Successfully imported 1 range(s). Errors: ', $result['message']);
        $this->assertStringContainsString('Row 1: Missing required fields.', $result['message']);
        $this->assertStringContainsString('Row 2: Missing required fields.', $result['message']);
        $this->assertStringContainsString('Row 3: Invalid field value or length.', $result['message']);
        $this->assertStringContainsString('Row 5: Invalid field value or length.', $result['message']);
        $this->assertStringNotContainsString('Row 6', $result['message']);
    }

    public function testObjectPayloadWithStringKeysIsNumberedByPosition(): void
    {
        $this->content = json_encode([
            'first' => ['country_id' => 'IN', 'state_name' => 'Delhi', 'zip_start' => '1', 'zip_end' => '2'],
            'second' => ['country_id' => 'IN'],
            'third' => ['country_id' => 'IND', 'state_name' => 'X', 'zip_start' => '1', 'zip_end' => '2'],
        ]);

        $result = $this->dispatch();

        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['imported']);
        $this->assertStringContainsString('Row 2: Missing required fields.', $result['message']);
        $this->assertStringContainsString('Row 3: Invalid field value or length.', $result['message']);
    }

    public function testErrorListIsCappedAtFive(): void
    {
        $this->content = json_encode(array_fill(0, 7, ['country_id' => 'IN']));

        $result = $this->dispatch();

        $this->assertSame(0, $result['imported']);
        $this->assertStringContainsString('Row 5:', $result['message']);
        $this->assertStringNotContainsString('Row 6:', $result['message']);
    }

    public function testRowSaveFailureIsReportedPerRow(): void
    {
        $this->content = json_encode([
            ['country_id' => 'IN', 'state_name' => 'Delhi', 'zip_start' => '1', 'zip_end' => '2'],
        ]);

        $result = $this->dispatch([], new \RuntimeException('Duplicate'));

        $this->assertTrue($result['success']);
        $this->assertSame(0, $result['imported']);
        $this->assertStringContainsString('Row 1: Duplicate', $result['message']);
    }
}
