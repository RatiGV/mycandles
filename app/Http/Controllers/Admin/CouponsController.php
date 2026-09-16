<?php

namespace App\Http\Controllers\Admin;

use App\Imports\CouponsImport;
use App\Models\Coupon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CouponsController extends BaseController
{
    public $data = []; // წარმოდგენის ფაილებზე მისამაგრებელი ინფორმაცია

    private $model; // მიმდინარე ინსტანციის მოდელი

    private $views_folder; // წარმოდგენების ფაილების საქაღალდე  მიმდინარე ინსტანციისათვის

    private $main_table; // მიმდინარე ინსტანციის ძირითადი ცხრილი

    /*
    * მარშრუტების სუფიქსი მიმდინარე ინსტანციისათვის, გამოიყენება ბმულების
    * გენერირებისათვის კონტროლერებსა და წარმოდგენის ფაილებში. ძირითადი პრეფიქსები,
    * რომლებიც დაერთვის: 'Add', 'Store', 'Edit', 'Update', 'Remove', 'Status', 'Ordering'
    */
    private $routes_suffix;

    public function __construct(Coupon $model)
    {
        parent::__construct();

        $this->model = $model;
        $this->routes_suffix = 'Coupons';
        $this->views_folder = 'Administrator.coupons';
        $this->main_table = 'coupons';
    }

    public static function main_columns()
    {
        $main_table_columns = Schema::getColumnListing(self::$main_table); // ძირითადი ცხრილის ყველა ველი

        // ძირითადი ცხრილის ის ველები, რომელთა შესაბამისი ელემენტების ავტო-დაგენერირებაც არ გვინდა წარმოდგენის შაბლონში
        $main_no_generate_columns = [
            'id',
            'created_at',
            'updated_at',
        ];

        // ძირითადი ცხრილის ის ველები, რომელთა შესაბამისი ელემენტების ავტო-დაგენერირებაც გვინდა წარმოდგენის შაბლონში
        return array_diff($main_table_columns, $main_no_generate_columns);
    }

    public function index()
    {
        $this->data['listing_columns'] = ['code', 'percent'];
        $this->data['items'] = $this->model::all();
        $this->data['routes_suffix'] = $this->routes_suffix;
        $this->data['main_table'] = $this->main_table;

        return view($this->views_folder.'.index', $this->data);
    }

    public function import()
    {
        $this->data['routes_suffix'] = $this->routes_suffix;

        return view($this->views_folder.'.import', $this->data);
    }

    public function upload(Request $request)
    {
        $this->validate($request, [
            'file' => 'required',
        ]);

        $extensions = ['xls', 'xlsx', 'xlm', 'xla', 'xlc', 'xlt', 'xlw'];
        $ext = $request->file('file')->getClientOriginalExtension();

        if (in_array($ext, $extensions)) {
            //$path = $request->file('file')->getRealPath();
            $path1 = $request->file('file')->store('temp');
            $path = storage_path('app').'/'.$path1;

            Excel::import(new CouponsImport, $path);

            $request->session()->flash('success', true);
        }

        return redirect()->route($this->routes_suffix);
    }
}
