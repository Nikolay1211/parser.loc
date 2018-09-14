<?php

namespace App\Modules\Crawler;


interface UrlInterface
{
    public function setLincParts($link);

    public function isSchemeParts();

    public function isDomainParts();

    public function isHostLinkParts();

    public function isPathLinkParts();

    public function isQueryLinkParts();

    public function segments();

    public function segmentsCount();

    public function removeFragment($link);

    public function removeSlash($link);

}