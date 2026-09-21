<?php

namespace App\Http\Controllers\Admin;

use App;
use App\Imports\ProductsImport;
use App\Models\Product;
use DB;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProductController extends BaseController
{
    public $data = []; // წარმოდგენის ფაილებზე მისამაგრებელი ინფორმაცია

    private $model; // მიმდინარე ინსტანციის მოდელი

    private $views_folder; // წარმოდგენების ფაილების საქაღალდე  მიმდინარე ინსტანციისათვის

    private $main_table; // მიმდინარე ინსტანციის ძირითადი ცხრილი

    private $translates_table; // მიმდინარე ინსტანციის სათარგმნი ცხრილი

    private $categories_model; // კატეგორიების მოდელი

    private $image_gallery_table; // მიმდინარე ინსტანციის ფოტო გალერიის ცხრილი

    /*
    * მარშრუტების სუფიქსი მიმდინარე ინსტანციისათვის, გამოიყენება ბმულების
    * გენერირებისათვის კონტროლერებსა და წარმოდგენის ფაილებში. ძირითადი პრეფიქსები,
    * რომლებიც დაერთვის: 'Add', 'Store', 'Edit', 'Update', 'Remove', 'Status', 'Ordering'
    */
    private $routes_suffix;

    public function __construct(Product $model)
    {
        parent::__construct();

        $this->model = $model;
        $this->routes_suffix = 'Products';
        $this->views_folder = 'Administrator.products.products';
        $this->main_table = 'products';
        $this->translates_table = 'products_translates';
        $this->categories_model = \App\Models\ProductCategory::class;
        $this->image_gallery_table = 'products_photos';
    }

    public function main_columns()
    {
        // ძირითადი ცხრილის ყველა ველი
        $main_table_columns = Schema::getColumnListing($this->main_table); // All colums of main table

        /* ძირითადი ცხრილის ის ველები, რომელთა შესაბამისი html
         * ელემენტების ავტოდაგენერირებაც არ გვინდა წარმოდგენის ფაილში
         */
        $main_no_generate_columns = [
            'id',
            'category_id',
            'image',
            'color_images',
            'sort',
            'status',
            'new_product',
            'available',
            'created_at',
            'updated_at',
        ];

        /* ძირითადი ცხრილის ის ველები, რომელთა შესაბამისი html
         * ელემენტების ავტოდაგენერირებაც გვინდა წარმოდგენის ფაილში
         */
        return array_diff($main_table_columns, $main_no_generate_columns);
    }

    public function translate_columns()
    {
        if (property_exists(__CLASS__, 'translates_table')) {
            $translates_table_columns = Schema::getColumnListing($this->translates_table);

            $translates_no_generate_columns = [
                'id',
                'parent_id',
                'lang',
                'created_at',
                'updated_at',
            ];

            return array_diff($translates_table_columns, $translates_no_generate_columns);
        } else {
            return [];
        }
    }

    protected $required_columns = ['title', 'description'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $this->model::whereNotNull('id');

        if ($request->has('status') && $request->get('status') != 1) {
            if ($request->get('status') == 2) {
                $query->where('status', 1);
            } else {
                $query->where('status', 0);
            }
        }

        if ($request->has('new_product') && $request->get('new_product') != 1) {
            if ($request->get('new_product') == 2) {
                $query->where('new_product', 1);
            } else {
                $query->where('new_product', 0);
            }
        }

        if ($request->has('available') && $request->get('available') != 1) {
            if ($request->get('available') == 2) {
                $query->where('available', 1);
            } else {
                $query->where('available', 0);
            }
        }

        if ($request->title) {
            $product_ids_by_title = DB::table('products_translates')
                ->where('title', 'like', "%$request->title%")
                ->where('lang', $this->configuration->admin_lang)->pluck('parent_id')->toArray();

            $query->whereIn('id', $product_ids_by_title);
        }

        if ($request->from) {
            $query->where('price', '>=', $request->from);
        }

        if ($request->to) {
            $query->where('price', '<=', $request->to);
        }

        $where_in = $query->pluck('id')->toArray();

        if (empty($where_in)) {
            $this->data['items'] = collect();
        } else {
            $this->data['items'] = $this->model::allItems($this->configuration->admin_lang, $status_on = false, $where_in, $where_in_cat = false, $paginate = true, $get = false);
        }

        $this->data['listing_columns'] = ['sort', 'status', 'new_product', 'available', 'image', 'title', 'price'];
        $this->data['routes_suffix'] = $this->routes_suffix;
        $this->data['main_table'] = $this->main_table;
        $this->data['translates_table'] = $this->translates_table;
        $this->data['categories'] = $this->categories_model::allItems('ka', true, false);

        return view($this->views_folder . '.index', $this->data);
    }

    public function create()
    {
        $this->data['routes_suffix'] = $this->routes_suffix;
        $this->data['main_columns'] = $this->main_columns();
        $this->data['translate_columns'] = $this->translate_columns();
        $this->data['required_columns'] = $this->required_columns;
        $this->data['categories'] = $this->categories_model::allItems('ka', true, false);

        return view($this->views_folder . '.add', $this->data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'translates.' . $this->configuration->admin_lang . '.title' => 'required',
            'translates.' . $this->configuration->admin_lang . '.description' => 'required',
            'category_id' => 'nullable|array',
            'price' => 'nullable|numeric|min:0',
            'image' => 'mimes:jpeg,jpg,png',
            'code' => 'nullable|string|unique:products,code',
        ]);

        $insert = $this->model->addItem($request);

        $request->session()->flash('last_edited_lang', $request->last_edited_lang);

        if (! $insert) {
            $request->session()->flash('error', true);

            return redirect()->route($this->routes_suffix);
        }

        $request->session()->flash('success', true);

        return redirect()->route($this->routes_suffix);
    }

    public function edit($id)
    {
        $item = $this->model->find($id);

        if (! $item) {
            return redirect()->back();
        }

        $this->data['routes_suffix'] = $this->routes_suffix;
        $this->data['main_columns'] = $this->main_columns();
        $this->data['required_columns'] = $this->required_columns;
        $this->data['translate_columns'] = $this->translate_columns();
        $this->data['item'] = $item;
        $this->data['model'] = $this->model;
        $this->data['main_table'] = $this->main_table;
        $this->data['image_gallery_table'] = $this->image_gallery_table;
        $this->data['categories'] = $this->categories_model::allItems('ka', true, false);

        return view($this->views_folder . '.edit', $this->data);
    }

    public function update(Request $request, $id)
    {
        $item = $this->model->find($id);

        if (! $item) {
            return redirect()->back();
        }

        $this->validate($request, [
            'translates.' . $this->configuration->admin_lang . '.title' => 'required',
            'translates.' . $this->configuration->admin_lang . '.description' => 'required',
            'category_id' => 'nullable|array',
            'price' => 'nullable|numeric|min:0',
            'image' => 'mimes:jpeg,jpg,png',
            'code' => 'nullable|string|unique:products,code,' . $item->id,
        ]);

        $update = $this->model->updateItem($request, $item);

        $request->session()->flash('last_edited_lang', $request->last_edited_lang);

        if (! $update) {
            $request->session()->flash('error', true);

            return redirect()->route('Edit' . $this->routes_suffix, $id);
        }

        $request->session()->flash('success', true);

        if ($request->stay) {
            return redirect()->route('Edit' . $this->routes_suffix, [
                'id' => $item->id,
                'page' => $request->page,
            ]);
        } else {
            return redirect()->to('/admin/products/product?page=' . $request->page);
        }
    }

    public function live_search(Request $request)
    {
        if ($request->ajax()) {
            $result = '';

            $ids_by_title = DB::table('products_translates')->where('title', 'like', "%$request->title%")
                ->where('lang', App::getLocale())->pluck('parent_id')->toArray();

            if (count($ids_by_title)) {
                $products = Product::allItems(App::getlocale(), $status_on = false, $ids_by_title, $where_in_cat = false, $paginate = false, $get = true);
            } else {
                $products = collect();
            }

            if ($products->count()) {
                foreach ($products as $product) {
                    $result .= '<li data-id="' . $product->id . '" class="result-li"><img src="' . $product->image . '">' . $product->title . '</li>';
                }
            } else {
                $result = '<li><i class="fas fa-exclamation-circle"></i> ' . trans('admin.no_found') . '</li>';
            }

            return $result;
        }
    }

    public function import()
    {
        $this->data['routes_suffix'] = $this->routes_suffix;

        return view($this->views_folder . '.import', $this->data);
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
            $path = storage_path('app') . '/' . $path1;

            Excel::import(new ProductsImport, $path);

            $request->session()->flash('success', true);
        }

        return redirect()->route($this->routes_suffix);
    }
}
