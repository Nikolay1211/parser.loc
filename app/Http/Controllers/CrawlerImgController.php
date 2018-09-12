<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CrawlerImgModel;
class CrawlerImgController extends Controller
{
    public function index()
    {
        $result=CrawlerImgModel::all()->sortBy('cont_img');

        return view('crawler.index',['images'=>$result]);
    }
}
