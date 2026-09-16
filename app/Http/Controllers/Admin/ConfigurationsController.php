<?php

namespace App\Http\Controllers\Admin;

use App\Models\Configuration;
use Cache;
use DB;
use Illuminate\Http\Request;

class ConfigurationsController extends BaseController
{
    public $data = []; // წარმოდგენის ფაილებზე მისამაგრებელი ინფორმაცია

    private $model;  // მიმდინარე ინსტანციის მოდელი

    private $views_folder; // წარმოდგენების ფაილების საქაღალდე  მიმდინარე ინსტანციისათვის

    private $main_table; // მიმდინარე ინსტანციის ძირითადი ცხრილი

    /*
    * მარშრუტების სუფიქსი მიმდინარე ინსტანციისათვის, გამოიყენება ბმულების
    * გენერირებისათვის კონტროლერებსა და წარმოდგენის ფაილებში. ძირითადი პრეფიქსები,
    * რომლებიც დაერთვის: 'Add', 'Store', 'Edit', 'Update', 'Remove', 'Status', 'Ordering'
    */
    private $routes_suffix;

    public function __construct(Configuration $model)
    {
        parent::__construct();

        $this->model = $model;
        $this->routes_suffix = 'Configurations';
        $this->views_folder = 'Administrator.configuration';
        $this->main_table = 'configurations';
    }

    public function edit()
    {
        $item = $this->model->find(1);

        if (! $item) {
            return redirect()->back();
        }

        $this->data['routes_suffix'] = $this->routes_suffix;
        $this->data['item'] = $item;
        $this->data['languages'] = $this->get_languages();
        $this->data['modules'] = [
            [
                'title' => 'sliders',
                'route' => 'Sliders',
            ],
            [
                'title' => 'products',
                'route' => 'Products',
            ],
            [
                'title' => 'product_categories',
                'route' => 'ProductCategories',
            ],
            [
                'title' => 'brands',
                'route' => 'Brands',
            ],
            [
                'title' => 'sales',
                'route' => 'Sales',
            ],
            [
                'title' => 'news',
                'route' => 'News',
            ],
            [
                'title' => 'photo_galleries',
                'route' => 'PhotoGalleries',
            ],
            [
                'title' => 'textpages',
                'route' => 'Textpages',
            ],
            [
                'title' => 'informations',
                'route' => 'Informations',
            ],
            [
                'title' => 'seos',
                'route' => 'Seos',
            ],
        ];

        $this->data['actions'] = [
            'create',
            'edit',
            'remove',
            'status',
            'ordering',
            'multi',
            'RemoveImages',
            'RemoveVideos',
            'remove_file',
        ];

        $this->data['config'] = [];
        $cache_info = DB::table('caches')->select(['module', 'minutes', 'expires_at'])->get();

        foreach ($cache_info as $c_info) {
            $this->data['config'][$c_info->module] = [
                'minutes' => $c_info->minutes,
                'expires_at' => $c_info->expires_at,
            ];
        }

        return view($this->views_folder.'.edit', $this->data);
    }

    public function update(Request $request)
    {
        $item = $this->model->find(1);

        if (! $item) {
            return redirect()->back();
        }

        $this->validate($request, [
            //
        ]);

        $update = $this->model->updateItem($request, $item);

        if (! $update) {
            $request->session()->flash('error', true);

            return redirect()->route('Edit'.$this->routes_suffix);
        }

        $request->session()->flash('success', true);

        return redirect()->route('Edit'.$this->routes_suffix);
    }

    public function remove_cache_key($key)
    {
        Cache::forget($key);
        DB::table('caches')->where('module', $key)->delete();

        return redirect()->back();
    }
}
