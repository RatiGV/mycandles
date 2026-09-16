<?php
namespace App\Http\Controllers\Client;

use App\Models\Slider;
use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Benefit;
use App\Models\News;
use App\Models\Partner;

class HomeController extends Controller
{
    public $data = [];

    public function index()
    {
        $this->data['sliders'] = Slider::allItems(locale(),true);

        $this->data['products'] = Product::with('trans')->whereNotNull('id')->where('status',1)->take(8)->get();

        $this->data['blogs'] = News::with('trans')->where('status',1)->take(4)->orderBy('created_at','desc')->get();

        $this->data['benefits'] = Benefit::allItems(locale(),true);

        $this->data['banners'] = Banner::allItems(locale(),true);

        $this->data['partners'] = Partner::allItems(locale(),true);

        return view('client.home.index',$this->data);
    }
}
