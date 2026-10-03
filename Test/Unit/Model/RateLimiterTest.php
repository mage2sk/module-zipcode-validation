<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Test\Unit\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Panth\ZipcodeValidation\Model\RateLimiter;
use PHPUnit\Framework\TestCase;

class RateLimiterTest extends TestCase
{
    private array $store = [];
    private array $saves = [];

    private function limiter($limit, $window = null, $ip = '10.0.0.1'): RateLimiter
    {
        $cache = $this->createStub(CacheInterface::class);
        $cache->method('load')->willReturnCallback(fn($key) => $this->store[$key] ?? false);
        $cache->method('save')->willReturnCallback(function ($data, $key, $tags = [], $lifetime = null) {
            $this->store[$key] = $data;
            $this->saves[] = [$key, $data, $tags, $lifetime];
            return true;
        });

        $config = $this->createStub(ScopeConfigInterface::class);
        $config->method('getValue')->willReturnCallback(
            static fn($path) => [RateLimiter::XML_PATH_LIMIT => $limit, RateLimiter::XML_PATH_WINDOW => $window][$path]
                ?? null
        );

        $remote = $this->createStub(RemoteAddress::class);
        $remote->method('getRemoteAddress')->willReturn($ip);

        return new RateLimiter($cache, $config, $remote);
    }

    public function testZeroLimitDisablesLimiting(): void
    {
        $limiter = $this->limiter('0');

        for ($i = 0; $i < 5; $i++) {
            $this->assertTrue($limiter->isAllowed('check'));
        }
        $this->assertSame([], $this->saves);
    }

    public function testRequestsBeyondLimitAreBlocked(): void
    {
        $limiter = $this->limiter('2', '60');

        $this->assertTrue($limiter->isAllowed('check'));
        $this->assertTrue($limiter->isAllowed('check'));
        $this->assertFalse($limiter->isAllowed('check'));
        $this->assertCount(2, $this->saves);
        $last = end($this->saves);
        $this->assertSame('2', $last[1]);
        $this->assertSame(60, $last[3]);
    }

    public function testInvalidWindowFallsBackToDefault(): void
    {
        $limiter = $this->limiter('3', '-5');

        $limiter->isAllowed('check');

        $this->assertSame(300, $this->saves[0][3]);
        $this->assertStringStartsWith('panth_zipcode_rl_', $this->saves[0][0]);
    }

    public function testActionsAndClientsHaveSeparateBuckets(): void
    {
        $limiter = $this->limiter('1', '60');
        $this->assertTrue($limiter->isAllowed('check'));
        $this->assertTrue($limiter->isAllowed('other'));
        $this->assertFalse($limiter->isAllowed('check'));

        $unknownClient = $this->limiter('1', '60', '');
        $this->assertTrue($unknownClient->isAllowed('check'));
        $this->assertCount(3, array_unique(array_column($this->saves, 0)));
    }
}
