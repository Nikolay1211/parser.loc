<?php

namespace Tests\Crawler;


use Tests\TestCase;
use App\Modules\Crawler\CrawlerImg;

class CrawlerImgTest extends TestCase
{
    public function testFilterElements()
    {
        $images = [
            'http://example.com/images/test1.jpg',
            'http://example.com/images/test2.jpg',
            'http://example.com/images/test3.jpg',
            'http://example.com/images/test4.jpg',
            'http://example.com/images/test5.jpg',
            'https://example.com/images/test6.jpg',
            'http://example.com/images/test7.jpg',
            'http://example.ru/images/test8.jpg',
        ];

        $Img = new CrawlerImg('http://example.com');
        $img_count = $Img->filterElements($images);

        $this->assertEquals($img_count,6);
    }


}