<?php

namespace Tests\Controllers;

use Tests\TestCase;

class CrawlerImgControllerTest extends TestCase {


    public function testIndex()
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        $response->assertViewHas('images');

        $images=$response->original->getData()['images'];

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $images);
    }

}