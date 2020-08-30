<?php


use PHPUnit\Framework\TestCase;
use ZM\Config\ZMConfig;

class ZMConfigTest extends TestCase
{
    public function setUp(): void
    {
        ZMConfig::setDirectory(__DIR__ . "/config");
    }

    public function testGet()
    {
        $this->assertIsArray(ZMConfig::get("test"));
    }

    public function testEnvSetting()
    {
        ZMConfig::env("production");
        $this->assertEquals("production", ZMConfig::get("test")["hello"]);
    }

    public function testNonExistEnv()
    {
        ZMConfig::env("development");
        $this->assertIsBool(ZMConfig::get("test"));
    }

    public function testGetKey() {
        $this->assertEquals("world", ZMConfig::get("test", "hello"));
        $this->assertNull(ZMConfig::get("test", "qwe"));
    }

    public function testJson() {
        $this->assertIsArray(ZMConfig::get("global"));
        $this->assertEquals("test", ZMConfig::get("global", "name"));
    }
}
