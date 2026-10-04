<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Test\Unit\Model;

use Magento\Directory\Model\Region;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Panth\ZipcodeValidation\Model\PincodeValidator;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange\Collection;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange\CollectionFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PincodeValidatorTest extends TestCase
{
    private const RANGES = [
        ['country_id' => 'IN', 'state_code' => 'DL', 'state_name' => 'Delhi',
            'zip_start' => '110001', 'zip_end' => '110096'],
        ['country_id' => 'IN', 'state_code' => 'MH', 'state_name' => 'Maharashtra',
            'zip_start' => '400001', 'zip_end' => '445402'],
        ['country_id' => 'US', 'state_code' => 'NY', 'state_name' => 'New York',
            'zip_start' => '10001', 'zip_end' => '14925'],
        ['country_id' => 'GB', 'state_code' => 'LDN', 'state_name' => 'London',
            'zip_start' => 'E1', 'zip_end' => 'EZ'],
        ['country_id' => 'CA', 'state_code' => 'ON', 'state_name' => 'Ontario',
            'zip_start' => 'K0A 0A0', 'zip_end' => 'P9Z 9Z9'],
    ];

    /** @var array<int, array<string, mixed>> */
    private array $filters = [];
    private int $collectionsCreated = 0;

    private function validator(
        array $config = [],
        array $flags = [],
        ?RegionFactory $regionFactory = null,
        array $rows = self::RANGES
    ): PincodeValidator {
        $factory = $this->createStub(CollectionFactory::class);
        $factory->method('create')->willReturnCallback(function () use ($rows) {
            $this->collectionsCreated++;
            $applied = new \stdClass();
            $applied->filters = [];
            $collection = $this->createStub(Collection::class);
            $collection->method('addFieldToFilter')->willReturnCallback(
                function ($field, $value) use ($applied, $collection) {
                    $applied->filters[$field] = $value;
                    $this->filters[] = [$field => $value];
                    return $collection;
                }
            );
            $collection->method('getIterator')->willReturnCallback(function () use ($applied, $rows) {
                $items = [];
                foreach ($rows as $row) {
                    if (isset($applied->filters['country_id'])
                        && $row['country_id'] !== $applied->filters['country_id']
                    ) {
                        continue;
                    }
                    $items[] = new DataObject($row);
                }
                return new \ArrayIterator($items);
            });
            return $collection;
        });

        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturnCallback(static fn($path) => $config[$path] ?? null);
        $scopeConfig->method('isSetFlag')->willReturnCallback(static fn($path) => !empty($flags[$path]));

        return new PincodeValidator(
            $regionFactory ?? $this->createStub(RegionFactory::class),
            $factory,
            $scopeConfig
        );
    }

    private function regionFactory(?string $code, $id = 5, bool $throw = false): RegionFactory
    {
        $region = new class ($throw) extends Region {
            private bool $fail;

            public function __construct(bool $fail)
            {
                $this->fail = $fail;
            }

            public function load($modelId, $field = null)
            {
                if ($this->fail) {
                    throw new \RuntimeException('db down');
                }
                return $this;
            }
        };
        $region->setData(['region_id' => $id, 'code' => $code]);
        $region->setIdFieldName('region_id');
        $factory = $this->createStub(RegionFactory::class);
        $factory->method('create')->willReturn($region);
        return $factory;
    }

    public function testFlagsReadStoreScopedConfig(): void
    {
        $validator = $this->validator([], [
            'zipcode_validation/general/enabled' => true,
            'zipcode_validation/general/enforce_on_order' => false,
        ]);

        $this->assertTrue($validator->isEnabled(1));
        $this->assertFalse($validator->isEnforceOnOrder(1));
    }

    public function testEmptyPincodeIsRejected(): void
    {
        $result = $this->validator()->validate(' - ', null, 'IN');

        $this->assertFalse($result['valid']);
        $this->assertSame('Please enter your postal/ZIP code.', $result['message']);
    }

    #[DataProvider('invalidIndianFormats')]
    public function testIndianFormatIsEnforced(string $pin): void
    {
        $result = $this->validator()->validate($pin, null, 'in');

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('6 digits', $result['message']);
    }

    public static function invalidIndianFormats(): array
    {
        return [
            'too short' => ['11000'],
            'too long' => ['1100011'],
            'leading zero' => ['010001'],
            'letters' => ['11000A'],
        ];
    }

    public function testValidPinReturnsMatchingState(): void
    {
        $result = $this->validator()->validate('110 001', null, 'IN');

        $this->assertTrue($result['valid']);
        $this->assertSame('Delhi', $result['state']);
        $this->assertSame('', $result['message']);
    }

    public function testPinOutsideAllRangesUsesDefaultErrorMessage(): void
    {
        $result = $this->validator()->validate('999999', null, 'IN');

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('verify this postal code', $result['message']);
    }

    public function testPinOutsideAllRangesUsesConfiguredErrorMessage(): void
    {
        $validator = $this->validator(['zipcode_validation/general/error_message' => '  Not deliverable  ']);

        $result = $validator->validate('999999', null, 'IN');

        $this->assertSame('Not deliverable', $result['message']);
    }

    public function testCountryWithoutRangesIsAcceptedAsIs(): void
    {
        $result = $this->validator()->validate('75001', null, 'FR');

        $this->assertSame(['valid' => true, 'message' => '', 'state' => ''], $result);
    }

    public function testNumericBoundsAreInclusive(): void
    {
        $validator = $this->validator();

        $this->assertTrue($validator->validate('110096', null, 'IN')['valid']);
        $this->assertTrue($validator->validate('400001', null, 'IN')['valid']);
        $this->assertFalse($validator->validate('110097', null, 'IN')['valid']);
    }

    public function testLongerCodeIsTruncatedToBoundLength(): void
    {
        $result = $this->validator()->validate('10001-1234', null, 'US');

        $this->assertTrue($result['valid']);
        $this->assertSame('New York', $result['state']);
    }

    public function testAlphanumericRangesCompareCaseInsensitively(): void
    {
        $validator = $this->validator();

        $this->assertSame('London', $validator->validate('e1 6an', null, 'GB')['state']);
        $this->assertSame('Ontario', $validator->validate('m5v 2t6', null, 'ca')['state']);
        $this->assertFalse($validator->validate('A1A1A1', null, 'CA')['valid']);
    }

    public function testRangeWithEmptyBoundNeverMatches(): void
    {
        $rows = [[
            'country_id' => 'DE', 'state_code' => 'BE', 'state_name' => 'Berlin',
            'zip_start' => '--', 'zip_end' => '19999',
        ]];

        $result = $this->validator([], [], null, $rows)->validate('10115', null, 'DE');

        $this->assertFalse($result['valid']);
    }

    public function testAlphaRegionCodeMatchingStateIsAccepted(): void
    {
        $result = $this->validator()->validate('400001', 'mh', 'IN');

        $this->assertTrue($result['valid']);
        $this->assertSame('Maharashtra', $result['state']);
    }

    public function testRegionMismatchNamesTheCorrectState(): void
    {
        $result = $this->validator()->validate('400001', 'DL', 'IN');

        $this->assertFalse($result['valid']);
        $this->assertSame(
            'This postal code is associated with Maharashtra. Please check your state/region selection.',
            $result['message']
        );
    }

    public function testNumericRegionIdIsResolvedThroughRegionModel(): void
    {
        $validator = $this->validator([], [], $this->regionFactory('dl'));

        $this->assertTrue($validator->validate('110001', '12', 'IN')['valid']);
        $this->assertFalse($validator->validate('400001', '12', 'IN')['valid']);
    }

    public function testUnknownNumericRegionSkipsTheStateCheck(): void
    {
        $validator = $this->validator([], [], $this->regionFactory(null, null));

        $this->assertTrue($validator->validate('400001', '999', 'IN')['valid']);
    }

    public function testRegionLookupFailureSkipsTheStateCheck(): void
    {
        $validator = $this->validator([], [], $this->regionFactory('DL', 5, true));

        $this->assertTrue($validator->validate('400001', '12', 'IN')['valid']);
    }

    public function testEmptyRegionStringSkipsTheStateCheck(): void
    {
        $this->assertTrue($this->validator()->validate('400001', '', 'IN')['valid']);
    }

    public function testRangesAreLoadedOncePerCountryAndFilteredByActiveAndCountry(): void
    {
        $validator = $this->validator();

        $validator->validate('110001', null, 'IN');
        $validator->validate('110002', null, 'IN');
        $this->assertTrue($validator->hasRanges(' in '));

        $this->assertSame(1, $this->collectionsCreated);
        $this->assertSame([['is_active' => 1], ['country_id' => 'IN']], $this->filters);
    }

    public function testHasRangesIsFalseForUnknownCountry(): void
    {
        $validator = $this->validator();

        $this->assertFalse($validator->hasRanges('FR'));
        $this->assertTrue($validator->hasRanges('US'));
        $this->assertSame(2, $this->collectionsCreated);
    }

    public function testGetStateByPincode(): void
    {
        $validator = $this->validator();

        $this->assertSame('Delhi', $validator->getStateByPincode('110001'));
        $this->assertNull($validator->getStateByPincode('999999'));
        $this->assertSame('', $validator->getStateByPincode('75001', 'FR'));
    }

    #[DataProvider('countryFormats')]
    public function testCountryFormatsAreCheckedBeforeTheRanges(string $country, string $code, string $message): void
    {
        $result = $this->validator()->validate($code, null, $country);

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString($message, $result['message']);
    }

    public static function countryFormats(): array
    {
        return [
            'US letters after digits' => ['US', '10001ABC', 'ZIP+4'],
            'US four digits' => ['US', '1000', '5 digits'],
            'US six digits' => ['US', '100011', '5 digits'],
            'CA digits only' => ['CA', '12345', 'A1A 1A1'],
            'CA wrong pattern' => ['CA', 'KKA0B1', 'A1A 1A1'],
            'GB digits only' => ['GB', '10001', 'SW1A 1AA'],
            'GB outward code only' => ['GB', 'W1', 'SW1A 1AA'],
            'IN letters' => ['IN', '11000A', '6 digits'],
        ];
    }

    #[DataProvider('wellFormedCodes')]
    public function testWellFormedCodesPassTheFormatCheck(string $country, string $code): void
    {
        $this->assertTrue($this->validator([], [], null, [])->validate($code, null, $country)['valid']);
    }

    public static function wellFormedCodes(): array
    {
        return [
            'US five digits' => ['US', '10001'],
            'US ZIP+4 with dash' => ['US', '10001-1234'],
            'CA with space and lower case' => ['CA', 'k1a 0b1'],
            'GB short outward' => ['GB', 'w2 1aa'],
            'GB letter district' => ['GB', 'SW1A 1AA'],
            'GB Girobank' => ['GB', 'GIR 0AA'],
            'country without format' => ['DE', '1-0-1-1-5'],
        ];
    }

    public function testUkOutwardCodesAreComparedInNaturalOrder(): void
    {
        $rows = [[
            'country_id' => 'GB', 'state_code' => '', 'state_name' => 'London West',
            'zip_start' => 'W1', 'zip_end' => 'W14',
        ]];
        $validator = $this->validator([], [], null, $rows);

        foreach (['W1 1AA', 'W1A 1AA', 'W2 1AA', 'W9 9ZZ', 'W10 5AA', 'W14 8XX'] as $code) {
            $this->assertTrue($validator->validate($code, null, 'GB')['valid'], $code);
        }
        foreach (['W15 1AA', 'WC1A 1AA', 'SW1A 1AA', 'E1 6AN'] as $code) {
            $this->assertFalse($validator->validate($code, null, 'GB')['valid'], $code);
        }
    }

    public function testUkFullPostcodeBoundsUseTheWholeCode(): void
    {
        $rows = [[
            'country_id' => 'GB', 'state_code' => '', 'state_name' => 'Parliament',
            'zip_start' => 'SW1A 0AA', 'zip_end' => 'SW1A 2ZZ',
        ]];
        $validator = $this->validator([], [], null, $rows);

        $this->assertTrue($validator->validate('sw1a 1aa', null, 'GB')['valid']);
        $this->assertFalse($validator->validate('SW1A 3AA', null, 'GB')['valid']);
    }

    public function testCanadianForwardSortationAreasUseNaturalOrder(): void
    {
        $rows = [[
            'country_id' => 'CA', 'state_code' => 'ON', 'state_name' => 'Toronto',
            'zip_start' => 'M1A', 'zip_end' => 'M9Z',
        ]];
        $validator = $this->validator([], [], null, $rows);

        $this->assertSame('Toronto', $validator->validate('m5v 3l9', null, 'CA')['state']);
        $this->assertFalse($validator->validate('K1A 0B1', null, 'CA')['valid']);
    }

    public function testRangeWithoutStateCodeAcceptsAnyRegion(): void
    {
        $rows = [[
            'country_id' => 'GB', 'state_code' => null, 'state_name' => 'London West',
            'zip_start' => 'W1', 'zip_end' => 'W14',
        ]];

        $result = $this->validator([], [], null, $rows)->validate('W2 1AA', 'LND', 'GB');

        $this->assertTrue($result['valid']);
        $this->assertSame('London West', $result['state']);
    }

    public function testFormatErrorIsReturnedEvenWithoutRanges(): void
    {
        $result = $this->validator([], [], null, [])->validate('ABCDE', null, 'US');

        $this->assertFalse($result['valid']);
        $this->assertSame('US ZIP codes must be 5 digits or ZIP+4 (e.g. 10001 or 10001-1234).', $result['message']);
    }
}
