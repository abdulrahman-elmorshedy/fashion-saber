<?php
namespace InnoShop\RestAPI\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Base TestCase for RestAPI tests.
 * Uses pure PHPUnit without Laravel dependencies.
 */
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * Setup method called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Teardown method called after each test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
