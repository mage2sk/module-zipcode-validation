<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Test\Unit\Plugin;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\QuoteManagement;
use Panth\ZipcodeValidation\Model\OrderAddressValidator;
use Panth\ZipcodeValidation\Plugin\QuoteSubmitPlugin;
use Panth\ZipcodeValidation\Plugin\ShippingInformationPlugin;
use PHPUnit\Framework\TestCase;

class AddressPluginsTest extends TestCase
{
    private function quote(bool $virtual, Address $address): Quote
    {
        $quote = $this->createStub(Quote::class);
        $quote->method('isVirtual')->willReturn($virtual);
        $quote->method('getShippingAddress')->willReturn($address);
        $quote->method('getStoreId')->willReturn('3');
        return $quote;
    }

    public function testQuoteSubmitValidatesShippingAddressWithStore(): void
    {
        $address = $this->createStub(Address::class);
        $validator = $this->createMock(OrderAddressValidator::class);
        $validator->expects($this->once())->method('assertValid')->with($address, 3);

        $result = (new QuoteSubmitPlugin($validator))
            ->beforeSubmit($this->createStub(QuoteManagement::class), $this->quote(false, $address));

        $this->assertNull($result);
    }

    public function testVirtualQuoteIsNotValidated(): void
    {
        $validator = $this->createMock(OrderAddressValidator::class);
        $validator->expects($this->never())->method('assertValid');

        $result = (new QuoteSubmitPlugin($validator))->beforeSubmit(
            $this->createStub(QuoteManagement::class),
            $this->quote(true, $this->createStub(Address::class))
        );

        $this->assertNull($result);
    }

    public function testQuoteSubmitPropagatesValidationFailure(): void
    {
        $validator = $this->createStub(OrderAddressValidator::class);
        $validator->method('assertValid')->willThrowException(new LocalizedException(__('bad postcode')));

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('bad postcode');

        (new QuoteSubmitPlugin($validator))->beforeSubmit(
            $this->createStub(QuoteManagement::class),
            $this->quote(false, $this->createStub(Address::class))
        );
    }

    public function testShippingInformationValidatesShippingAddress(): void
    {
        $address = $this->createStub(AddressInterface::class);
        $info = $this->createStub(ShippingInformationInterface::class);
        $info->method('getShippingAddress')->willReturn($address);

        $validator = $this->createMock(OrderAddressValidator::class);
        $validator->expects($this->once())->method('assertValid')->with($address);

        $result = (new ShippingInformationPlugin($validator))
            ->beforeSaveAddressInformation($this->createStub(ShippingInformationManagement::class), 5, $info);

        $this->assertNull($result);
    }
}
