<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Crawler\CrawlerImg as CrawlerImg;
class Crawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:start {url} {--query} {--level=} {--search=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $levelMax;

    protected $searchMax;

    protected $baseUrl;

    protected $query;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->baseUrl = $this->argument('url');
        $this->levelMax =$this->option('level');
        $this->searchMax =$this->option('search');
        $this->query=$this->option('query');

        $this->info('Запущено сканирование страниц.');
        $this->info('Стартовая странца:'.$this->baseUrl);
        $this->info('Максимальное количество страниц:'.$this->searchMax);
        $this->info('Максимальная глубина вложености: '. $this->levelMax);

        $crawler = new CrawlerImg($this->baseUrl,$this->levelMax,$this->searchMax,$this->query);

        $this->line('Сканирую карту сайта...');

        $crawler->create();

        $this->line('Карта сайта построена...');
        $this->line('Считаю теги <img>');

        $crawler->CrawledImg();

        $this->info('Сканирование завершено!!!');
    }

}
