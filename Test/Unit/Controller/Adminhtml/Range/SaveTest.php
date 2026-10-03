<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Test\Unit\Controller\Adminhtml\Range;

use Panth\ZipcodeValidation\Controller\Adminhtml\Range\Save;
use PHPUnit\Framework\Attributes\DataProvider;

class SaveTest extends ControllerTestCase
{
    private const VALID = [
        'country_id' => ' in ',
        'state_code' => ' DL ',
        'state_name' => ' Delhi ',
        'zip_start' => ' 110001 ',
        'zip_end' => '110096',
    ];

    private function controller(array $existing = [], ?\Throwable $saveError = null): Save
    {
        return new Save($this->context(), $this->rangeFactory(), $this->resource($existing, $saveError));
    }

    public function testEmptyPostRedirectsToGrid(): void
    {
        $this->controller()->execute();

        $this->assertSame(['*/*/', []], $this->redirect);
        $this->assertSame([], $this->saved);
    }

    public function testNewRangeIsNormalisedAndSaved(): void
    {
        $this->post = self::VALID;

        $this->controller()->execute();

        $this->assertCount(1, $this->saved);
        $this->assertSame([
            'country_id' => 'IN',
            'state_code' => 'DL',
            'state_name' => 'Delhi',
            'zip_start' => '110001',
            'zip_end' => '110096',
            'is_active' => 1,
        ], array_intersect_key($this->saved[0]->getData(), self::VALID + ['is_active' => 1]));
        $this->assertSame(['Range has been saved.'], $this->messagesOf('success'));
        $this->assertSame(['*/*/', []], $this->redirect);
    }

    public function testExplicitInactiveFlagIsKept(): void
    {
        $this->post = self::VALID + ['is_active' => '0'];

        $this->controller()->execute();

        $this->assertSame(0, $this->saved[0]->getData('is_active'));
    }

    public function testBackParamReturnsToEditWithNewId(): void
    {
        $this->post = self::VALID;
        $this->params = ['back' => 'edit'];

        $this->controller()->execute();

        $this->assertSame(['*/*/edit', ['id' => 100]], $this->redirect);
    }

    public function testExistingRangeIsUpdated(): void
    {
        $this->post = self::VALID + ['range_id' => '7'];

        $this->controller([7 => ['country_id' => 'IN', 'state_name' => 'Old']])->execute();

        $this->assertCount(1, $this->saved);
        $this->assertSame(7, (int) $this->saved[0]->getId());
        $this->assertSame('Delhi', $this->saved[0]->getData('state_name'));
    }

    public function testMissingExistingRangeShowsError(): void
    {
        $this->post = self::VALID + ['range_id' => '9'];

        $this->controller()->execute();

        $this->assertSame([], $this->saved);
        $this->assertSame(['This range no longer exists.'], $this->messagesOf('error'));
        $this->assertSame(['*/*/', []], $this->redirect);
    }

    #[DataProvider('invalidValues')]
    public function testValidationErrorsRedirectBackToNewForm(array $override, string $expected): void
    {
        $this->post = array_merge(self::VALID, $override);

        $this->controller()->execute();

        $this->assertSame([], $this->saved);
        $this->assertContains($expected, $this->messagesOf('error'));
        $this->assertSame(['*/*/new', []], $this->redirect);
    }

    public static function invalidValues(): array
    {
        $zip = 'is required, can have at most 20 characters and must contain letters or digits.';
        return [
            'country' => [['country_id' => 'IND'], 'Please select a valid country.'],
            'state name empty' => [['state_name' => '  '],
                'State/Region Name is required and can have at most 255 characters.'],
            'state name long' => [['state_name' => str_repeat('a', 256)],
                'State/Region Name is required and can have at most 255 characters.'],
            'state code long' => [['state_code' => 'ABCDEFGHIJK'],
                'State/Region Code can have at most 10 characters.'],
            'zip start symbols only' => [['zip_start' => '---'], 'ZIP/PIN Start ' . $zip],
            'zip end too long' => [['zip_end' => str_repeat('9', 21)], 'ZIP/PIN End ' . $zip],
        ];
    }

    public function testValidationErrorsOnExistingRangeRedirectToEdit(): void
    {
        $this->post = ['range_id' => 4, 'country_id' => '', 'state_name' => '', 'zip_start' => '', 'zip_end' => ''];

        $this->controller([4 => ['country_id' => 'IN']])->execute();

        $this->assertCount(4, $this->messagesOf('error'));
        $this->assertSame(['*/*/edit', ['id' => 4]], $this->redirect);
    }

    public function testSaveFailureShowsMessageAndReturnsToEdit(): void
    {
        $this->post = self::VALID;

        $this->controller([], new \RuntimeException('Duplicate entry'))->execute();

        $this->assertSame(['Duplicate entry'], $this->messagesOf('error'));
        $this->assertSame(['*/*/edit', ['id' => 0]], $this->redirect);
    }
}
