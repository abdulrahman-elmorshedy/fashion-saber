<?php
namespace InnoShop\Front\Tests\Middleware;

use InnoShop\Front\Middleware\SetFrontLocale;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class SetFrontLocaleTest extends TestCase
{
    #[Test]
    public function test_middleware_exists(): void
    {
        $this->assertTrue(class_exists(SetFrontLocale::class));
    }

    #[Test]
    public function test_has_handle_method(): void
    {
        $this->assertTrue(method_exists(SetFrontLocale::class, 'handle'));
    }

    #[Test]
    public function test_handle_method_is_public(): void
    {
        $reflection = new ReflectionClass(SetFrontLocale::class);
        $method     = $reflection->getMethod('handle');
        $this->assertTrue($method->isPublic());
    }

    #[Test]
    public function test_handle_method_has_correct_parameters(): void
    {
        $reflection = new ReflectionClass(SetFrontLocale::class);
        $method     = $reflection->getMethod('handle');
        $parameters = $method->getParameters();

        $this->assertCount(2, $parameters);
        $this->assertEquals('request', $parameters[0]->getName());
        $this->assertEquals('next', $parameters[1]->getName());
    }

    #[Test]
    public function test_middleware_can_be_instantiated(): void
    {
        $middleware = new SetFrontLocale;
        $this->assertInstanceOf(SetFrontLocale::class, $middleware);
    }

    #[Test]
    public function test_handle_method_returns_mixed(): void
    {
        $reflection = new ReflectionClass(SetFrontLocale::class);
        $method     = $reflection->getMethod('handle');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertEquals('mixed', $returnType->getName());
    }
}
