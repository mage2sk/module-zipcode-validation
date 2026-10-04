<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Test\Unit\Controller\Adminhtml\Range;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Message\ManagerInterface;
use Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange as ZipcodeRangeResource;
use Panth\ZipcodeValidation\Model\ZipcodeRange;
use Panth\ZipcodeValidation\Model\ZipcodeRangeFactory;
use PHPUnit\Framework\TestCase;

/**
 * Shared wiring for range admin controllers: records redirects, messages and resource calls.
 */
abstract class ControllerTestCase extends TestCase
{
    protected array $post = [];
    protected array $params = [];
    protected string $content = '';
    protected array $messages = [];
    protected ?array $redirect = null;

    /** @var ZipcodeRange[] */
    protected array $saved = [];
    /** @var ZipcodeRange[] */
    protected array $deleted = [];

    protected function context(): Context
    {
        $request = $this->createStub(Http::class);
        $request->method('getPostValue')->willReturnCallback(fn() => $this->post);
        $request->method('getContent')->willReturnCallback(fn() => $this->content);
        $request->method('getParam')->willReturnCallback(
            fn($key, $default = null) => $this->params[$key] ?? $default
        );

        $redirect = $this->createStub(Redirect::class);
        $redirect->method('setPath')->willReturnCallback(function ($path, $params = []) use ($redirect) {
            $this->redirect = [$path, $params];
            return $redirect;
        });
        $redirectFactory = $this->createStub(RedirectFactory::class);
        $redirectFactory->method('create')->willReturn($redirect);

        $messages = $this->createStub(ManagerInterface::class);
        foreach (['addSuccessMessage' => 'success', 'addErrorMessage' => 'error'] as $method => $type) {
            $messages->method($method)->willReturnCallback(function ($message) use ($type, $messages) {
                $this->messages[] = [$type, (string) $message];
                return $messages;
            });
        }

        $context = $this->createStub(Context::class);
        $context->method('getRequest')->willReturn($request);
        $context->method('getResultRedirectFactory')->willReturn($redirectFactory);
        $context->method('getMessageManager')->willReturn($messages);
        return $context;
    }

    /**
     * Builds a range model whose resource "load" fills it from $existing[id].
     */
    protected function rangeFactory(): ZipcodeRangeFactory
    {
        $factory = $this->createStub(ZipcodeRangeFactory::class);
        $factory->method('create')->willReturnCallback(static function () {
            $range = new class extends ZipcodeRange {
                public function __construct()
                {
                }
            };
            $range->setIdFieldName('range_id');
            return $range;
        });
        return $factory;
    }

    /**
     * @param array<int, array> $existing rows by id
     */
    protected function resource(array $existing = [], ?\Throwable $saveError = null): ZipcodeRangeResource
    {
        $resource = $this->createStub(ZipcodeRangeResource::class);
        $resource->method('load')->willReturnCallback(function ($object, $id) use ($existing, $resource) {
            if (isset($existing[$id])) {
                $object->setData($existing[$id] + ['range_id' => $id]);
            }
            return $resource;
        });
        $resource->method('save')->willReturnCallback(function ($object) use ($saveError, $resource) {
            if ($saveError !== null) {
                throw $saveError;
            }
            if (!$object->getId()) {
                $object->setId(100 + count($this->saved));
            }
            $this->saved[] = $object;
            return $resource;
        });
        $resource->method('delete')->willReturnCallback(function ($object) use ($resource) {
            $this->deleted[] = $object;
            return $resource;
        });
        return $resource;
    }

    protected function messagesOf(string $type): array
    {
        return array_values(array_map(
            static fn($m) => $m[1],
            array_filter($this->messages, static fn($m) => $m[0] === $type)
        ));
    }
}
