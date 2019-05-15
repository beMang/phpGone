<?php

namespace Test;

use \phpGone\TwigExtensions\AssetsExtension;

class AssetExtensionsTest extends \PHPUnit\Framework\TestCase
{
    protected $assetExtension;

    public static function setUpBeforeClass() :void
    {
        require_once(__DIR__ . '/../vendor/autoload.php');
    }
    
    public function setUp() :void
    {
        $request = new \GuzzleHttp\Psr7\ServerRequest('GET', '/');
        $app = new \phpGone\Core\Application(__DIR__ . '/../app/config.php', $request);
        $extension = new AssetsExtension($app);
        $this->assetExtension = $extension;
    }

    public function testCss()
    {
        $result = $this->assetExtension->css('test');
        $expected = '<link rel="stylesheet" type="text/css" href="/app/assets/css/test.css">';
        $this->assertEquals($expected, $result);
        $result = $this->assetExtension->css(['test', 'test2']);
        $this->assertSTringContainsString('<link rel="stylesheet" type="text/css" href="/app/assets/css/test.css">', $result);
        $this->assertSTringContainsString('<link rel="stylesheet" type="text/css" href="/app/assets/css/test2.css">', $result);
    }

    public function testJs()
    {
        $result = $this->assetExtension->js('test');
        $expected = '<script src="/app/assets/js/test.js"></script>';
        $this->assertEquals($expected, $result);
        $result = $this->assetExtension->js(['test', 'test2']);
        $this->assertSTringContainsString('<script src="/app/assets/js/test.js"></script>', $result);
        $this->assertSTringContainsString('<script src="/app/assets/js/test2.js"></script>', $result);
    }
}
