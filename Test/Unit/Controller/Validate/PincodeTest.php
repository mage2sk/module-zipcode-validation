<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Test\Unit\Controller\Validate;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Panth\ZipcodeValidation\Controller\Validate\Pincode;
use Panth\ZipcodeValidation\Model\PincodeValidator;
use Panth\ZipcodeValidation\Model\RateLimiter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PincodeTest extends TestCase
{
    private array $params = [];
    private ?array $data = null;
    private array $headers = [];
    private ?int $code = null;

    /**
     * @param PincodeValidator&MockObject $validator
     */
    private function dispatch(PincodeValidator $validator, bool $allowed = true): array
    {
        $request = $this->createStub(Http::class);
        $request->method('getParam')->willReturnCallback(
            fn($key, $default = null) => $this->params[$key] ?? $default
        );
        $context = $this->createStub(Context::class);
        $context->method('getRequest')->willReturn($request);

        $json = $this->createStub(Json::class);
        $json->method('setData')->willReturnCallback(function ($data) use ($json) {
            $this->data = $data;
            return $json;
        });
        $json->method('setHeader')->willReturnCallback(function ($name, $value) use ($json) {
            $this->headers[$name] = $value;
            return $json;
        });
        $json->method('setHttpResponseCode')->willReturnCallback(function ($code) use ($json) {
            $this->code = $code;
            return $json;
        });
        $factory = $this->createStub(JsonFactory::class);
        $factory->method('create')->willReturn($json);

        $limiter = $this->createStub(RateLimiter::class);
        $limiter->method('isAllowed')->willReturn($allowed);

        (new Pincode($context, $factory, $validator, $limiter))->execute();
        return $this->data;
    }

    /**
     * @return PincodeValidator&MockObject
     */
    private function validator(bool $enabled = true): PincodeValidator
    {
        $validator = $this->createMock(PincodeValidator::class);
        $validator->method('isEnabled')->willReturn($enabled);
        return $validator;
    }

    public function testDisabledModuleAlwaysPassesAndSendsNoCacheHeaders(): void
    {
        $validator = $this->validator(false);
        $validator->expects($this->never())->method('validate');
        $this->params = ['pincode' => '999999'];

        $this->assertSame(['valid' => true, 'message' => ''], $this->dispatch($validator));
        $this->assertSame('no-cache', $this->headers['Pragma']);
        $this->assertStringContainsString('no-store', $this->headers['Cache-Control']);
    }

    public function testRateLimitedRequestGets429(): void
    {
        $validator = $this->validator();
        $validator->expects($this->never())->method('validate');
        $this->params = ['pincode' => '110001'];

        $data = $this->dispatch($validator, false);

        $this->assertSame(429, $this->code);
        $this->assertTrue($data['rate_limited']);
        $this->assertTrue($data['valid']);
        $this->assertSame('Too many requests. Please try again later.', $data['message']);
    }

    public function testEmptyOrNonScalarPincodeIsTreatedAsEmpty(): void
    {
        $validator = $this->validator();
        $validator->expects($this->never())->method('validate');

        $this->params = ['pincode' => '   '];
        $this->assertSame(['valid' => true, 'message' => ''], $this->dispatch($validator));

        $this->params = ['pincode' => ['110001']];
        $this->assertSame(['valid' => true, 'message' => ''], $this->dispatch($validator));
    }

    public function testOverlongPincodeIsRejected(): void
    {
        $validator = $this->validator();
        $validator->expects($this->never())->method('validate');
        $this->params = ['pincode' => str_repeat('1', 21)];

        $this->assertSame(
            ['valid' => false, 'message' => 'Please enter a valid postal/ZIP code.'],
            $this->dispatch($validator)
        );
    }

    public function testInvalidCountryIsRejected(): void
    {
        $validator = $this->validator();
        $validator->expects($this->never())->method('validate');
        $this->params = ['pincode' => '10001', 'country_id' => 'USA'];

        $this->assertSame(['valid' => false, 'message' => 'Please select a valid country.'], $this->dispatch($validator));
    }

    public function testDefaultsToIndiaAndPassesRegion(): void
    {
        $validator = $this->validator();
        $validator->expects($this->once())->method('validate')
            ->with('110001', 'DL', 'IN')
            ->willReturn(['valid' => true, 'message' => '', 'state' => 'Delhi']);
        $this->params = ['pincode' => ' 110001 ', 'region_id' => 'DL'];

        $this->assertSame(['valid' => true, 'message' => '', 'state' => 'Delhi'], $this->dispatch($validator));
    }

    public function testUnsafeOrLongRegionIsDropped(): void
    {
        $validator = $this->validator();
        $validator->expects($this->exactly(2))->method('validate')
            ->with('10001', null, 'US')
            ->willReturn(['valid' => true]);

        $this->params = ['pincode' => '10001', 'country_id' => 'us', 'region_id' => '<script>'];
        $this->dispatch($validator);

        $this->params = ['pincode' => '10001', 'country_id' => 'us', 'region_id' => str_repeat('a', 21)];
        $this->assertSame(['valid' => true], $this->dispatch($validator));
    }
}
