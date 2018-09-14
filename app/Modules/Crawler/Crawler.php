<?php

namespace App\Modules\Crawler;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class Crawler implements CrawlerInterface
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


    public function __construct($baseUrl,$levelMax=null,$searchMax=null,$query=false)
    {
        $this->baseUrl = $baseUrl;

        $this->scheme = parse_url($baseUrl, PHP_URL_SCHEME);

        $this->domain = parse_url($baseUrl, PHP_URL_HOST);

        $this->searchMax = $searchMax;

        $this->levelMax = $levelMax;

        $this->query=$query;

        $this->Url = new Url($baseUrl);

    }

    public function create()
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


    public function processLinksOnPage(DomCrawler $crawler) {

        $links = $this->getLinksOnPage( $crawler );

        $links = $this->filter($links);

        foreach ( $links as $key => $link )
        {
            $this->processLink($link);
        }
        return $this;
    }


    public function getLinksOnPage( DomCrawler $crawler )
    {
        $links = $crawler->filter('a')->each(function (DomCrawler $node) {

            return $node->link()->getUri();

        });

        return $links;
    }


    public function filter($links)
    {
        $linksSkan=[];

        foreach ( $links as $link )
        {
            $this->Url->setLincParts($link);

            if (!$this->Url->isHostLinkParts() || $this->Url->isSchemeParts() || $this->Url->isDomainParts() || $this->isLinkIgnore($link))
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


    public function processLink($link) {

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


    public function crawlerStop()
    {
        if ($this->isSearchMax()) {
            return false;
        }
        return $this->getDomainLinksCount() >= $this->getSearchMax();
    }


    public function getDomainLinks()
    {
        return $this->domainLinks;
    }


    public function getDomainLinksCount()
    {
        return $this->domainLinksCount;
    }


    public function getSearchMax()
    {
        return $this->searchMax;
    }


    public function isSearchMax()
    {
        return is_null($this->searchMax);
    }


    public function getLevelMax()
    {
        return $this->levelMax;
    }

    public function isLevelMax()
    {
        return is_null($this->levelMax);
    }

    public function isLinkIgnore($link)
    {
        return str_contains($link,$this->linkIgnore);
    }

    public function getQuery()
    {
        return $this->query;
    }


}