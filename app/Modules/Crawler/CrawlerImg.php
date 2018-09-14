<?php

namespace App\Modules\Crawler;

use Goutte\Client;
use App\Models\CrawlerImgModel;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class CrawlerImg extends Crawler
{
    /** @var int $imgCount */
    protected $imgCount=0;

    public function __construct($baseUrl, $levelMax = null, $searchMax = null, bool $query = false)
    {
        parent::__construct($baseUrl, $levelMax, $searchMax, $query);
    }


    public function CrawledImg()
    {
        $client= new Client();

        $links=$this->getDomainLinks();

        foreach($links as $key=>$link)
        {
            $timeStart=microtime(true);

            echo '   => '.$link .PHP_EOL;

            $crawler=$client->request('GET',$link) ;

            $images= $this->eachImg($crawler);

            $this->filterElements($images);

            $this->saveImg($link,$timeStart);
        }
    }


    public function eachImg(DomCrawler $crawler)
    {
        $images = $crawler->filter( 'img' )->each( function ( DomCrawler $node ){
            return $node->image()->getUri();
        });
        return $images;
    }


    public function filterElements($images)
    {
        $this->imgCount=0;

        foreach ( $images as $img )
        {
            $this->Url->setLincParts($img);

            if (!$this->Url->isHostLinkParts() || $this->Url->isSchemeParts() || $this->Url->isDomainParts())
            {
                continue;
            }
            $this->imgCount++;
        }
        return $this->imgCount;
    }

    private function saveImg($link,$timeStart)
    {
        $Img = new CrawlerImgModel();
        $Img->page_link = $link;
        $Img->cont_img = $this->imgCount;
        $Img->time_load = round(microtime(true)-$timeStart,5);
        $Img->save();
    }

}