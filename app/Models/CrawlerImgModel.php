<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrawlerImgModel extends Model
{
    protected $table='crawler_img';

    protected $fillable = [
        'page_link', 'cont_img', 'time_load',
    ];
}
