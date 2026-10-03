<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Test\Unit\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;
use Panth\ZipcodeValidation\Model\OrderAddressValidator;
use Panth\ZipcodeValidation\Model\PincodeValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderAddressValidatorTest extends TestCase
{
    private function address(?string $country, ?string $postcode, $regionId = null): AddressInterface
    {
        $address = $this->createStub(AddressInterface::class);
        $address->method('getCountryId')->willReturn($country);
        $address->method('getPostcode')->willReturn($postcode);
        $address->method('getRegionId')->willReturn($regionId);
        return $address;
    }

    /**
     * @return PincodeValidator&MockObject
     */
    private function pincode(bool $enabled = true, bool $enforce = true, bool $ranges = true): PincodeValidator
    {
        $validator = $this->createMock(PincodeValidator::class);
        $validator->method('isEnabled')->willReturn($enabled);
        $validator->method('isEnforceOnOrder')->willReturn($enforce);
        $validator->method('hasRanges')->willReturn($ranges);
        return $validator;
    }

    public function testNullAddressIsIgnored(): void
    {
        $validator = $this->pincode();
        $validator->expects($this->never())->method('validate');

        (new OrderAddressValidator($validator))->assertValid(null);
    }

    public function testDisabledModuleSkipsValidation(): void
    {
        $validator = $this->pincode(false, true);
        $validator->expects($this->never())->method('validate');

        (new OrderAddressValidator($validator))->assertValid($this->address('IN', '999999'), 1);
    }

    public function testNotEnforcedOnOrderSkipsValidation(): void
    {
        $validator = $this->pincode(true, false);
        $validator->expects($this->never())->method('validate');

        (new OrderAddressValidator($validator))->assertValid($this->address('IN', '999999'), 1);
    }

    public function testMissingCountryOrCountryWithoutRangesIsSkipped(): void
    {
        $validator = $this->pincode(true, true, false);
        $validator->expects($this->never())->method('validate');
        $sut = new OrderAddressValidator($validator);

        $sut->assertValid($this->address('', '1'));
        $sut->assertValid($this->address('FR', '1'));
    }

    public function testValidAddressPassesNormalisedValues(): void
    {
        $validator = $this->pincode();
        $validator->expects($this->once())->method('validate')
            ->with('110001', '7', 'IN')
            ->willReturn(['valid' => true]);

        (new OrderAddressValidator($validator))->assertValid($this->address(' in ', ' 110001 ', '7'), 1);
    }

    public function testZeroRegionIsPassedAsNull(): void
    {
        $validator = $this->pincode();
        $validator->expects($this->once())->method('validate')
            ->with('10001', null, 'US')
            ->willReturn(['valid' => true]);

        (new OrderAddressValidator($validator))->assertValid($this->address('US', '10001', 0));
    }

    public function testInvalidAddressThrowsWithValidatorMessage(): void
    {
        $validator = $this->pincode();
        $validator->expects($this->once())->method('validate')
            ->willReturn(['valid' => false, 'message' => 'Wrong state']);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('The shipping address postcode cannot be accepted: Wrong state');

        (new OrderAddressValidator($validator))->assertValid($this->address('IN', '400001', '3'));
    }
}
