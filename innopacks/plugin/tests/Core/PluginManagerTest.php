<?php
namespace InnoShop\Plugin\Tests\Core;

use InnoShop\Plugin\Core\PluginManager;
use InnoShop\Plugin\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PluginManagerTest extends TestCase
{
    #[Test]
    public function test_plugin_manager_class_exists(): void
    {
        $this->assertTrue(class_exists(PluginManager::class));
    }

    #[Test]
    public function test_has_get_plugins_method(): void
    {
        $this->assertTrue(method_exists(PluginManager::class, 'getPlugins'));
    }

    #[Test]
    public function test_has_get_enabled_plugins_method(): void
    {
        $this->assertTrue(method_exists(PluginManager::class, 'getEnabledPlugins'));
    }

    #[Test]
    public function test_has_get_plugin_method(): void
    {
        $this->assertTrue(method_exists(PluginManager::class, 'getPlugin'));
    }

    #[Test]
    public function test_has_get_plugin_or_fail_method(): void
    {
        $this->assertTrue(method_exists(PluginManager::class, 'getPluginOrFail'));
    }

    #[Test]
    public function test_has_check_active_method(): void
    {
        $this->assertTrue(method_exists(PluginManager::class, 'checkActive'));
    }

    #[Test]
    public function test_has_import_method(): void
    {
        $this->assertTrue(method_exists(PluginManager::class, 'import'));
    }
}
