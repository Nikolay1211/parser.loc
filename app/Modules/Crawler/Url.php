<?php
/**
 * Created by PhpStorm.
 * User: Стажер
 * Date: 13.09.2018
 * Time: 14:40
 */

namespace App\Modules\Crawler;


class Url implements UrlInterface
{

    /** @var string */
    protected $scheme;

    /** @var string */
    protected $domain;

    /** @var array */
    protected $linkParts;


    public function __construct($url)
    {
        $this->scheme = parse_url($url, PHP_URL_SCHEME);

        $this->domain = parse_url($url, PHP_URL_HOST);
    }

    public function setLincParts($link)
    {
        return $this->linkParts= parse_url( $link);
    }

    public function isSchemeParts()
    {
        return $this->linkParts['scheme'] !== $this->scheme;
    }

    public function isDomainParts()
    {
        return $this->linkParts['host'] !== $this->domain;
    }

    public function isEmptyHostLinkParts()
    {
        return empty($this->linkParts['host']);
    }

    public function isPathLinkParts()
    {
        return isset($this->linkParts['path']);
    }

    public function isQueryLinkParts()
    {
        return isset($this->linkParts['query']);
    }

    public function segments()
    {
        return explode('/',$this->linkParts['path']);
    }

    public function segmentsCount()
    {
        return count($this->segments())-1;
    }

    public function removeFragment($link)
    {
        return explode('#', $link)[0];
    }

    public function removeSlash($link)
    {
        return $link[strlen($link)-1] !== '/' ? $link : substr($link,0,-1);
    }
}