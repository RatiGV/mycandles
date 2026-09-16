<?php

namespace App\Http\Controllers;

use App;
use App\Models\Brand;
use App\Models\Information;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Seo;
use Cache;
use DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use LaravelLocalization;
use Session;
use View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $lang; // მიმდინარე ენა

    protected $seo_routes; // ის მარშრუტები, რომლებსაც ჭირდება SEO ტეგები

    protected $config; // json ფაილში შენახული კონფიგურაციული პარამეტრები : ინფორმაცია ქეშირებების შესახებ ...

    protected $available_langs; // ხელმისაწვდომი ენები

    protected $categories; // პირველი დონის კატეგორიები

    public function __construct(Request $request)
    {
        $this->lang = App::getLocale();
        $this->seo_routes = ['index', 'news', 'products'];
        $this->available_langs = LaravelLocalization::getSupportedLocales();
        $this->config = file_exists(public_path('config.json')) ?
            json_decode(file_get_contents(public_path('config.json')), true) : false;

        View::share('info', Cache::has('info') ? Cache::get('info')[$this->lang] : Information::with('translate')->first());
        View::share('lang', $this->lang);
        View::share('brands', Cache::has('brands') ? Cache::get('brands')[$this->lang] : Brand::allItems($this->lang, $status_on = true));
        View::share('contact_info', Cache::has('informations') ? Cache::get('informations')[$this->lang] : Information::getItemInfo(3, $this->lang));
    }

    public function get_seo($route = null)
    {
        if (Cache::has('seos')) {
            $seo = Cache::get('seos')[$this->lang]->firstWhere('route', $route);
        } else {
            $seo = Seo::getItemInfo($route, $this->lang);
        }

        return $seo;
    }
}
