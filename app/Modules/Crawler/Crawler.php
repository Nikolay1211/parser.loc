<?php

namespace App\Modules\Crawler;

use Goutte\Client;
use App\Console\Commands\Crawler as Commands;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

abstract class Crawler
{
    /** @var string */
    protected $baseUrl;

    /** @var string */
    protected $domain;

    /** @var string */
    protected $scheme;

    /** @var boolean */
    protected $query;

    /** @var int|null */
    protected $searchMax = null;

    /** @var int|null */
    protected $levelMax = null;

    /** @var array */
    protected $domainLinks;

    /** @var array */
    protected $linksAll;

    /** @var array */
    protected $linkIgnore=['.jpg','.jpeg','.png','.gif','.txt','@'];

    /** @var int */
    protected $domainLinksCount = 0;

    /** @var Url  */
    protected $Url;


    abstract protected function selectElements(Commands $output);

    public function __construct($baseUrl,$levelMax=null,$searchMax=null,$query)
    {
        $this->baseUrl = $baseUrl;

        $this->scheme = parse_url($baseUrl, PHP_URL_SCHEME);

        $this->domain = parse_url($baseUrl, PHP_URL_HOST);

        $this->searchMax = $searchMax;

        $this->levelMax = $levelMax;

        $this->query=$query;

        $this->Url = new Url($baseUrl);
    }

    protected function start()
    {
        $client= new Client();

        $crawler=$client->request('GET',$this->baseUrl) ;

        $this->processLinksOnPage( $crawler);

        while (!empty( $this->linksAll )) {

            $client->getHistory()->clear();

            $url = array_pop( $this->linksAll );

            $crawler = $client->request( 'GET', $url );

            $this->processLinksOnPage( $crawler);
        }
        return $this;
    }


    protected function processLinksOnPage(DomCrawler $crawler) {

        $links = $this->getLinksOnPage( $crawler );

        foreach ( $links as $key => $link )
        {
            $this->processLink($link);
        }
        return $this;
    }


    protected function getLinksOnPage( DomCrawler $crawler )
    {
        $links = $crawler->filter('a')->each(function (DomCrawler $node) {

            return $node->link()->getUri();

        });

        return $this->filter($links);
    }


    protected function filter($links)
    {
        $linksSkan=[];

        foreach ( $links as $key => $link )
        {
            $this->Url->setLincParts($link);

            if ($this->Url->isEmptyHostLinkParts() || $this->Url->isSchemeParts() || $this->Url->isDomainParts() || $this->isLinkIgnore($link))
            {
                continue;
            }

            if( $this->Url->isPathLinkParts()  && !$this->isLevelMax() && $this->Url->segmentsCount() > $this->getLevelMax() )
            {
                continue;
            }

            if( $this->Url->isQueryLinkParts() && !$this->getQuery())
            {
                continue;
            }

            $link=$this->Url->removeFragment($link);

            $link=$this->Url->removeSlash($link);

            $linksSkan[]=$link;
        }

        return array_values( $linksSkan );
    }


    protected function processLink($link) {

        if ( empty( $this->domainLinks[$link] ) )
        {
            if($this->crawlerStop())
            {
                $this->linksAll=null;
            }
            else
            {
                $this->linksAll[]=$link;

                $this->domainLinks[$link] =$link;

                $this->domainLinksCount++;
            }
        }

        return $this;
    }


    protected function crawlerStop()
    {
        if ($this->isSearchMax()) {
            return false;
        }
        return $this->getDomainLinksCount() >= $this->getSearchMax();
    }


    protected function getDomainLinks()
    {
        return $this->domainLinks;
    }


    protected function getDomainLinksCount()
    {
        return $this->domainLinksCount;
    }


    protected function getSearchMax()
    {
        return $this->searchMax;
    }


    protected function isSearchMax()
    {
        return is_null($this->searchMax);
    }


    public function getLevelMax()
    {
        return $this->levelMax;
    }

    protected function isLevelMax()
    {
        return is_null($this->levelMax);
    }

    protected function isLinkIgnore($link)
    {
        return str_contains($link,$this->linkIgnore);
    }

    protected function getQuery()
    {
        return $this->query;
    }

}