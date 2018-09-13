<?php

namespace App\Modules\Crawler;

use Goutte\Client;
use App\Console\Commands\Crawler as Commands;
use App\Models\CrawlerImgModel;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class CrawlerImg extends Crawler
{
    /** @var int $imgCount */
    protected $imgCount;



    public function __construct($baseUrl, $levelMax = null, $searchMax = null, $query)
    {
        parent::__construct($baseUrl, $levelMax, $searchMax, $query);

    }


    public function startCrawlImg(Commands $output)
    {
        $this->start();

        $output->line('Карта сайта построена...');
        $output->line('Считаю теги <img>');

        $this->selectElements($output);

        $output->info('Сканирование завершено!!!');
    }


    protected function selectElements(Commands $output)
    {
        $client= new Client();

        $links=$this->getDomainLinks();

        foreach($links as $link)
        {
            $output->line('   => '.$link);

            $timeStart=microtime(true);

            $crawler=$client->request('GET',$link) ;

            $images = $crawler->filter( 'img' )->each( function ( DomCrawler $node ){
                return $node->image()->getUri();
            });

            $this->filterElements($images);

            $Img = new CrawlerImgModel();
            $Img->page_link = $link;
            $Img->cont_img = $this->imgCount;
            $Img->time_load = round(microtime(true)-$timeStart,5);
            $Img->save();
        }
    }


    protected function filterElements($images)
    {
        foreach ( $images as $key => $img )
        {
            $this->Url->setLincParts($img);

            if ($this->Url->isEmptyHostLinkParts() || $this->Url->isSchemeParts() || $this->Url->isDomainParts())
            {
                continue;
            }

            $this->imgCount++;
        }
    }

}