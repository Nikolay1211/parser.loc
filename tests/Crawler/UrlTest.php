<?php

namespace Tests\Crawler;

use Tests\TestCase;
use App\Modules\Crawler\Url;


class UrlTest extends TestCase
{
    /** @var Url */
    protected $Url;

    protected function setUp()
    {
        parent::setUp();

        $this->Url= new Url('http://example.com');
    }

    public function testSetLincParts()
    {
        $res=$this->Url->setLincParts('http://example.com/test/?page=2');

        $this->assertEquals($res['scheme'], 'http');
        $this->assertEquals($res['host'], 'example.com');
        $this->assertEquals($res['path'], '/test/');
        $this->assertEquals($res['query'],'page=2');

    }
    public function testIsSchemeParts()
    {
        $this->Url->setLincParts('http://example.com/test/?page=2');
        $false = $this->Url->isSchemeParts();

        $this->Url->setLincParts('https://example.com/test/?page=2');
        $true = $this->Url->isSchemeParts();

        $this->assertFalse($false);
        $this->assertTrue($true);
    }

    public function testIsDomainParts()
    {
        $this->Url->setLincParts('http://example.com/test/?page=2');
        $false = $this->Url->isDomainParts();

        $this->Url->setLincParts('http://example.ru/');
        $true = $this->Url->isDomainParts();

        $this->assertFalse($false);
        $this->assertTrue($true);
    }
    public function testIsHostLinkParts()
    {
        $this->Url->setLincParts('http://example.com/test/?page=2');
        $true = $this->Url->isHostLinkParts();

        $this->Url->setLincParts('/test/?page=2');
        $false = $this->Url->isHostLinkParts();

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    public function testIsPathLinkParts()
    {
        $this->Url->setLincParts('http://example.com/test/?page=2');
        $true = $this->Url->isPathLinkParts();

        $this->Url->setLincParts('http://example.com');
        $false = $this->Url->isPathLinkParts();

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    public function testIsQueryLinkParts()
    {
        $this->Url->setLincParts('http://example.com/test/?page=2');
        $true = $this->Url->isQueryLinkParts();

        $this->Url->setLincParts('http://example.com/test/');
        $false = $this->Url->isQueryLinkParts();

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    public function testSegments()
    {
        $this->Url->setLincParts('http://example.com/test1/test2/');
        $res = $this->Url->segments();

        $this->assertEquals($res[0],'');
        $this->assertEquals($res[1],'test1');
        $this->assertEquals($res[2],'test2');

        $this->Url->setLincParts('http://example.com/test1/test2');
        $res1 = $this->Url->segments();

        $this->assertEquals($res1[0],'');
        $this->assertEquals($res1[1],'test1');
        $this->assertEquals($res1[2],'test2');
    }

    public function testSegmentsCount()
    {
        $this->Url->setLincParts('http://example.com/test1/test2/');
        $res1 = $this->Url->segments();

        $this->Url->setLincParts('http://example.com/test1/test2');
        $res2 = $this->Url->segments();

        $this->assertEquals(count($res1)-1,2);
        $this->assertEquals(count($res2)-1,2);
    }

    public function testRemoveFragment()
    {
        $res=$this->Url->removeFragment('http://example.com/test1/test2#fragment');

        $this->assertEquals($res,'http://example.com/test1/test2');
    }

    public function testRemoveSlash()
    {
        $res1=$this->Url->removeSlash('http://example.com/test1/');
        $res2=$this->Url->removeSlash('http://example.com/test2');

        $this->assertEquals($res1,'http://example.com/test1');
        $this->assertEquals($res2,'http://example.com/test2');

    }
}