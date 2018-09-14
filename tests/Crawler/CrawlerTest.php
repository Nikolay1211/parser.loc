<?php

namespace Tests\Crawler;

use Tests\TestCase;
use App\Modules\Crawler\CrawlerImg;


class CrawlerTest extends TestCase
{

    protected $Crawler;

    public function filter()
    {

        $links=[
            'http://example.com/test1/#fragment',
            'http://example.com',
            'http://example.com/test/?page=2',
            'http://example.ru/',
            'http://example.com/test1/test2'
        ];

        $Img=new CrawlerImg('http://example.com');
        $res1=$Img->filter($links);

        $Img=new CrawlerImg('http://example.com',1);
        $res2=$Img->filter($links);

        $Img=new CrawlerImg('http://example.com',null,null,true);
        $res3=$Img->filter($links);

        $this->assertEquals(count($res1),3);
        $this->assertEquals(count($res2),2);
        $this->assertEquals(count($res3),4);
    }

    public function testCrawlerStop()
    {
        $Img=new CrawlerImg('http://example.com');
        $false=$Img->crawlerStop();

        $Img=new CrawlerImg('http://example.com',null,2);
        $false1=$Img->crawlerStop();

        $Img=new CrawlerImg('http://example.com',null,2);
        $Img->processLink('http://example.com/test1');
        $Img->processLink('http://example.com/test2');
        $true=$Img->crawlerStop();

        $this->assertFalse($false);
        $this->assertFalse($false1);
        $this->assertTrue($true);

    }


    public function testGetDomainLinks()
    {
        $Img=new CrawlerImg('http://example.com');
        $Img->processLink('http://example.com/test1');
        $Img->processLink('http://example.com/test1');
        $Img->processLink('http://example.com/test2');
        $res=$Img->GetDomainLinks();

        $this->assertEquals(count($res),2);
        $this->assertEquals($res['http://example.com/test1'],'http://example.com/test1');
        $this->assertEquals($res['http://example.com/test2'],'http://example.com/test2');
    }


    public function testGetDomainLinksCount()
    {
        $Img=new CrawlerImg('http://example.com');
        $Img->processLink('http://example.com/');
        $Img->processLink('http://example.com/test1');
        $Img->processLink('http://example.com/test1');
        $Img->processLink('http://example.com/test2');
        $res = $Img->getDomainLinksCount();

        $this->assertEquals($res,3);

    }

    public function testGetSearchMax()
    {
        $Img=new CrawlerImg('http://example.com',5,2);
        $max=$Img->getSearchMax();
        $this->assertEquals($max,2);
    }

    public function testGetQuery()
    {
        $Img=new CrawlerImg('http://example.com',5,2,true);
        $query=$Img->getQuery();
        $this->assertTrue($query);
    }

    public function testIsSearchMax()
    {
        $Img = new CrawlerImg('http://example.com');
        $true = $Img->isSearchMax();

        $Img = new CrawlerImg('http://example.com',5,2);
        $false = $Img->isSearchMax();

        $this->assertTrue($true);
        $this->assertFalse($false);
    }


    public function testGetLevelMax()
    {
        $Img=new CrawlerImg('http://example.com',5,2);
        $max=$Img->getLevelMax();
        $this->assertEquals($max,5);
    }

    public function testIsLevelMax()
    {
        $Img = new CrawlerImg('http://example.com');
        $true = $Img->isLevelMax();

        $Img = new CrawlerImg('http://example.com',5,2);
        $false = $Img->isLevelMax();

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    public function testIsLinkIgnore()
    {
        $Img = new CrawlerImg('http://example.com');
        $true = $Img->isLinkignore('http://example.com/image.jpg');
        $false = $Img->isLinkignore('http://example.com/test1');

        $this->assertTrue($true);
        $this->assertFalse($false);
    }


}