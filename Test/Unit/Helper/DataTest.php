<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Test\Unit\Helper;

use Magento\Framework\App\Helper\Context;
use Panth\ZipcodeValidation\Helper\Data;
use Panth\ZipcodeValidation\Model\PincodeValidator;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public function testIndianValidationForcesCountryIn(): void
    {
        $validator = $this->createMock(PincodeValidator::class);
        $validator->expects($this->once())->method('validate')
            ->with('110001', '5', 'IN')->willReturn(['valid' => true, 'state' => 'Delhi']);

        $helper = new Data($this->createStub(Context::class), $validator);

        $this->assertSame(['valid' => true, 'state' => 'Delhi'], $helper->validateIndianPincode('110001', '5'));
    }

    public function testGenericValidationPassesCountry(): void
    {
        $validator = $this->createMock(PincodeValidator::class);
        $validator->expects($this->once())->method('validate')
            ->with('10001', null, 'US')->willReturn(['valid' => false, 'message' => 'x']);

        $helper = new Data($this->createStub(Context::class), $validator);

        $this->assertFalse($helper->validateZipcode('10001', null, 'US')['valid']);
    }

    public function testStateLookupDelegatesWithIndiaDefault(): void
    {
        $validator = $this->createMock(PincodeValidator::class);
        $validator->expects($this->once())->method('getStateByPincode')
            ->with('400001', 'IN')->willReturn('Maharashtra');

        $helper = new Data($this->createStub(Context::class), $validator);

        $this->assertSame('Maharashtra', $helper->getStateByPincode('400001'));
    }
}
