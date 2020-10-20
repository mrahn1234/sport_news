<?php
namespace App\Scraper;

use App\Helper\CrawlerHelper;
use Illuminate\Support\Facades\Config;
use App\Models\Category;
use App\Models\Image;
use Goutte\Client;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\News;
use App\Models\Tag;
use Exception;


class Fo4{

    private $service_url = null;
    public function __construct()
    {
        $this->service_url = Config::get('app._SERVICE_URL');
    }
    
    public function scrape()
    {
        try{
            $client = new Client();
            $crawler = $client->request('GET', 'https://fo4.garena.vn/tin-tuc');
            $GLOBALS['categories'] = [];
            array_push(
                $GLOBALS['categories'], 
                Category::where(['name' => 'E-sports'])->first()->id,
                Category::where(['name' => 'Fifa online 4'])->first()->id,
            );
            $each_crawler = $crawler->filter('.list-news .news');
            if($each_crawler->count() > 0){
                $each_crawler->each(
                    function(Crawler $node){
                        if ($node->filter('img')->count() <= 0 ) return;
                        else{
                            $title_img = $node->filter('img')->attr('src');
                            $title =  $node->filter('h5')->text();
                            $summary = $node->filter('div .new-caption')->text();
                            $detail_href = $node->filter('h5 a')->attr('href');
                            $detail_client = new Client();
                            $detail_crawler = $detail_client->request('GET', $detail_href);
    
                            // get datetime content
                            
                            // xóa ký tự 'h' và -''
                            $datetime = str_replace('-', '', str_replace('h', ':', trim(($detail_crawler->filter('div.time-new'))->text())));
                            $datetime = now()->createFromFormat('H:i d/m/Y', $datetime, 'GMT+7');
    
                            //get img content
                            $GLOBALS['had_news_image'] = false;
                            $GLOBALS['images'] = [];
                            $detail_crawler->filter('.content-detail-news img')->each(function (Crawler $node) {
                                $src = $node->attr('src');
                                if($GLOBALS['had_news_image'] === true) return;
                                else{
                                    if(Image::where(['src' => $src])->first() === null){
                                        $image = Image::create([
                                            'src' => $node->attr('src'),
                                            'description' =>  'Fifa online 4',
                                        ]);
        
                                        array_push($GLOBALS['images'], $image);
                                    }
                                    else{
                                        $GLOBALS['had_news_image'] = true;
                                        $GLOBALS['images'] = [];
                                        return;
                                    }
                                }
                            });
                            $content = $detail_crawler->filter('.content-detail-news p')->each(function (Crawler $node) {
                                return '<p>' . $node->text() . '</p>';
                            });
                            $content = implode(' ', $content);
                            $db_content_monthDay = Category::where(['name' => 'Fifa online 4'])->first()->news()->get()
                            ->whereBetween('date_publish', [now()->subMonths(1), now()->addDay()])->pluck('content');
                            if ($db_content_monthDay->count() > 0) {
                                $request_servce = Http::post($this->service_url . '/check_similarity', [
                                    'from_db' => $db_content_monthDay,
                                    'data_check' => $content,
                                ]);
                                if ((!boolval($request_servce->body()) && trim($content) != "" ) || (empty($GLOBALS['images']) && $GLOBALS['had_news_image'] === false )) {
                                    $news = new News;
                                    $news->title = $title;
                                    $news->title_img = $title_img;
                                    $news->summary = $summary;
                                    $news->content = $content;
                                    $news->date_publish = $datetime;
                                    $news->status = 1;
                                    $news->save();
                                    $news->images()->saveMany($GLOBALS['images']);
                                    $news->categories()->attach($GLOBALS['categories']);
                                }
                                echo $request_servce->body();
                            } else {
                                if (trim($content) != "" || (empty($GLOBALS['images']) && $GLOBALS['had_news_image'] === false )) {
                                    $news = new News;
                                    $news->title = $title;
                                    $news->title_img = $title_img;
                                    $news->summary = $summary;
                                    $news->content = $content;
                                    $news->date_publish = $datetime;
                                    $news->status = 1;
                                    $news->save();
                                    $news->images()->saveMany($GLOBALS['images']);
                                    $news->categories()->attach($GLOBALS['categories']);
                                }
                            }
                            $GLOBALS['images'] = [];
                            $GLOBALS['had_news_image'] = false;
                        }
                        }
                        
                );
            }
            $GLOBALS['categories'] = [];
        }catch(Exception $e){
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
    }
}