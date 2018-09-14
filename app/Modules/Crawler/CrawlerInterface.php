<?php


namespace App\Modules\Crawler;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;
interface CrawlerInterface
{
    public function create();

    public function processLinksOnPage(DomCrawler $crawler);

    public function getLinksOnPage( DomCrawler $crawler );

    public function filter($links);

    public function processLink($link);

    public function crawlerStop();

    public function getDomainLinks();

    public function getDomainLinksCount();

    public function getSearchMax();

    public function isSearchMax();

    public function getLevelMax();

    public function isLevelMax();

    public function isLinkIgnore($link);

    public function getQuery();

}