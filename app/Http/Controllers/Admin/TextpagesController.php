<?php

namespace App\Http\Controllers\Admin;

use App\Models\Textpage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TextpagesController extends BaseController
{
    public $data = []; // წარმოდგენის ფაილებზე მისამაგრებელი ინფორმაცია

    private $model; // მიმდინარე ინსტანციის მოდელი

    private $views_folder; // წარმოდგენების ფაილების საქაღალდე  მიმდინარე ინსტანციისათვის

    private $main_table; // მიმდინარე ინსტანციის ძირითადი ცხრილი

    private $translates_table; // მიმდინარე ინსტანციის სათარგმნი ცხრილი

    private $image_gallery_table; // მიმდინარე ინსტანციის ფოტო გალერიის ცხრილი

    private $video_gallery_table; // მიმდინარე ინსტანციის ვიდეო გალერიის ცხრილი

    /*
    * მარშრუტების სუფიქსი მიმდინარე ინსტანციისათვის, გამოიყენება ბმულების
    * გენერირებისათვის კონტროლერებსა და წარმოდგენის ფაილებში. ძირითადი პრეფიქსები,
    * რომლებიც დაერთვის: 'Add', 'Store', 'Edit', 'Update', 'Remove', 'Status', 'Ordering'
    */
    private $routes_suffix;

    public function __construct(Textpage $model)
    {
        parent::__construct();

        $this->model = $model;
        $this->routes_suffix = 'Textpages';
        $this->views_folder = 'Administrator.textpages';
        $this->main_table = 'textpages';
        $this->translates_table = 'textpages_translates';
        $this->image_gallery_table = 'textpages_photos';
        $this->video_gallery_table = 'textpages_videos';
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
            'image',
            'sort',
            'status',
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

    public function index()
    {
        $this->data['listing_columns'] = ['title', 'image']; // <th> ელემენტები
        $this->data['items'] = $this->model->allItems($this->configuration->admin_lang, $status_on = false);
        $this->data['routes_suffix'] = $this->routes_suffix;

        return view($this->views_folder.'.index', $this->data);
    }

    public function create()
    {
        $this->data['routes_suffix'] = $this->routes_suffix;
        $this->data['main_columns'] = $this->main_columns();
        $this->data['required_columns'] = $this->required_columns;
        $this->data['translate_columns'] = $this->translate_columns();

        return view($this->views_folder.'.add', $this->data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'translates.'.$this->configuration->admin_lang.'.title' => 'required',
            'translates.'.$this->configuration->admin_lang.'.description' => 'required',
            'image' => 'mimes:jpeg,jpg,png',
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
        $this->data['video_gallery_table'] = $this->video_gallery_table;

        return view($this->views_folder.'.edit', $this->data);
    }

    public function update(Request $request, $id)
    {
        $item = $this->model->find($id);

        if (! $item) {
            return redirect()->back();
        }

        $this->validate($request, [
            'translates.'.$this->configuration->admin_lang.'.title' => 'required',
            'translates.'.$this->configuration->admin_lang.'.description' => 'required',
            'image' => 'mimes:jpeg,jpg,png',
        ]);

        $update = $this->model->updateItem($request, $item);

        $request->session()->flash('last_edited_lang', $request->last_edited_lang);

        if (! $update) {
            $request->session()->flash('error', true);

            return redirect()->route('Edit'.$this->routes_suffix, $id);
        }

        $request->session()->flash('success', true);

        if ($request->stay) {
            return redirect()->route('Edit'.$this->routes_suffix, $id);
        } else {
            return redirect()->route($this->routes_suffix);
        }
    }
}
