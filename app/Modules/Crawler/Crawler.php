<?php

namespace App\Modules\Crawler;

use Goutte\Client;
use App\Console\Commands\Crawler as Commands;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

abstract class Crawler
{
    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var boolean
     */

    protected $query;
    /**
     * @var int
     */
    protected $searchMax;

    /**
     * @var int
     */
    protected $levelMax;
    /**
     * @var array $domainLinks
     */
    protected $domainLinks;

    /**
     * @var array $linksAll
     */
    protected $linksAll;

    /**
     * @var array
     */
    protected $linkIgnore=['.jpg','.jpeg','.png','.gif','.txt','@'];

    abstract protected function selectElements(Commands $output);

    public function __construct($baseUrl,$levelMax=null,$searchMax=null,$query)
    {

        $this->scheme = parse_url($baseUrl, PHP_URL_SCHEME);
        $this->domain = parse_url($baseUrl, PHP_URL_HOST);
        $this->searchMax = $searchMax;
        $this->levelMax = $levelMax;
        $this->query=$query;
    }

    public function start($linkStart)
    {
        $this->crawlerLinkStart($linkStart);

    }
    protected function crawlerLinkStart($linkStart)
    {
        $client= new Client();

        $crawler=$client->request('GET',$linkStart) ;

        $this->processLinksOnPage( $crawler);

        while (!empty( $this->linksAll )) {

            $client->getHistory()->clear();

            $url = array_pop( $this->linksAll );

            $crawler = $client->request( 'GET', $url );

            $this->processLinksOnPage( $crawler);
        }
    }

    protected function processLinksOnPage(DomCrawler $crawler) {

        $links = $this->getLinksOnPage( $crawler );

        foreach ( $links as $key => $link )
        {
            $this->processLink($link);
        }
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
            $linkParts = parse_url( $link);

            if ($this->comparisonHost($linkParts))
            {
                unset( $links[$key] );
            }
            elseif( str_contains($link,$this->linkIgnore) )
            {
                unset( $links[$key] );
            }
            elseif( isset($linkParts['path'])  && !empty($this->levelMax) && count(explode('/',$linkParts['path'])) > $this->levelMax )
            {
                unset( $links[$key] );
            }
            elseif( isset($linkParts['query']) && !$this->query )
            {
                unset( $links[$key] );
            }
            else
            {
                $linksSkan[]=$this->removeFragment($link);
            }
        }

        return array_values( $linksSkan );
    }

    protected function comparisonHost($linkParts)
    {
        if ( empty( $linkParts['host'] ) || $linkParts['host'] !== $this->domain || $linkParts['scheme'] !== $this->scheme )
        {
            return true;
        }
        return false;
    }

    protected function removeFragment($link)
    {
        $links=explode('#', $link);
        return $links[0];
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
            }
        }

    }

    protected function crawlerStop()
    {
        if(!empty($this->searchMax) && count($this->domainLinks)==$this->searchMax)
        {
            return true;
        }
        return false;
    }

    protected function getDomainLinks()
    {
        return $this->domainLinks;
    }

}