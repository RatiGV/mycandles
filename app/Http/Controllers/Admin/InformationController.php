<?php

namespace App\Http\Controllers\Admin;

use App\Models\Information;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class InformationController extends BaseController
{
    public $data = []; // წარმოდგენის ფაილებზე მისამაგრებელი ინფორმაცია

    private $model;  // მიმდინარე ინსტანციის მოდელი

    private $views_folder; // წარმოდგენების ფაილების საქაღალდე  მიმდინარე ინსტანციისათვის

    private $main_table; // მიმდინარე ინსტანციის ძირითადი ცხრილი

    private $translates_table; // მიმდინარე ინსტანციის სათარგმნი ცხრილი

    /*
    * მარშრუტების სუფიქსი მიმდინარე ინსტანციისათვის, გამოიყენება ბმულების
    * გენერირებისათვის კონტროლერებსა და წარმოდგენის ფაილებში. ძირითადი პრეფიქსები,
    * რომლებიც დაერთვის: 'Add', 'Store', 'Edit', 'Update', 'Remove', 'Status', 'Ordering'
    */
    private $routes_suffix;

    public function __construct(Information $model)
    {
        parent::__construct();

        $this->model = $model;
        $this->routes_suffix = 'Informations';
        $this->views_folder = 'Administrator.information';
        $this->main_table = 'informations';
        $this->translates_table = 'informations_translates';
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
            'logo',
            'logo_for_admin',
            'favicon',
            'login_bg',
            'sort',
            'status',
            'created_at',
            'updated_at',
            'longitude',
            'latitude',
            'pixel',
            'analytics',
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

    protected $required_columns = [];

    public function edit()
    {
        $item = $this->model->find(3);

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

        return view($this->views_folder.'.edit', $this->data);
    }

    public function update(Request $request)
    {
        $item = $this->model->find(3);

        if (! $item) {
            return redirect()->back();
        }

        $this->validate($request, [
            'translates.'.$this->configuration->admin_lang.'.title' => 'required',
            'translates.*.slogan' => 'string|max:90',
            'logo' => 'mimes:jpeg,jpg,png',
        ]);

        $update = $this->model->updateItem($request, $item);

        $request->session()->flash('last_edited_lang', $request->last_edited_lang);

        if (! $update) {
            $request->session()->flash('error', true);

            return redirect()->route('Edit'.$this->routes_suffix);
        }

        $request->session()->flash('success', true);

        if ($request->stay) {
            return redirect()->route('Edit'.$this->routes_suffix);
        } else {
            return redirect()->route('AdminMainPage');
        }
    }
}
