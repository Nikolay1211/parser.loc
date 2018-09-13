<?php

namespace App\Modules\Crawler;

use Goutte\Client;
use App\Console\Commands\Crawler as Commands;
use App\Models\CrawlerImgModel;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
class CrawlerImg extends Crawler
{
    /**
     * @var int $imgCount
     */
    protected $imgCount;
    protected $elements;

    public function __construct($baseUrl, $levelMax = null, $searchMax = null, $query)
    {
        parent::__construct($baseUrl, $levelMax, $searchMax, $query);
    }
    
    public function startCrawlImg($linkStart,Commands $output)
    {
        $output->line('Сканирую карту сайта...');

        $this->start($linkStart);

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

            $crawlerImg=new CrawlerImgModel();
            $crawlerImg->page_link=$link;
            $crawlerImg->cont_img=$this->imgCount;
            $crawlerImg->time_load=round(microtime(true)-$timeStart,5);

            if(!$crawlerImg->save()){
                $output->error('Ошибка сохранения данных!!!');
            }
        }
    }

    protected function filterElements($images)
    {
        foreach ( $images as $key => $img )
        {
            $linkParts = parse_url($img);

            if ($this->comparisonHost($linkParts)) {
                unset($images[$key]);
            }
        }

        $this->imgCount=count($images);
    }

}
