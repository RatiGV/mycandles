<?php
namespace App\Http\Controllers\Admin;
use App\Models\Information;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
class InformationController extends BaseController
{
    public $data = []; // წარმოდგენის ფაილებზე მისამაგრებელი ინფორმაცია
    private $model;  // მიმდინარე ინსტანციის მოდელი
    private $views_folder; // წარმოდგენების ფაილების საქაღალდე მიმდინარე ინსტანციისათვის
    private $main_table; // მიმდინარე ინსტანციის ძირითადი ცხრილი
    private $translates_table; // მიმდინარე ინსტანციის სათარგმნი ცხრილი
    /*
    * მარშრუტების სუფიქსი მიმდინარე ინსტანციისათვის, გამოიყენება ბმულების
    * გენერირებისათვის კონტროლერებსა და წარმოდგენის ფაილებში.
    */
    private $routes_suffix;
    protected $required_columns = ['title'];
    public function __construct(Information $model)
    {
        parent::__construct();
        $this->model = $model;
        $this->routes_suffix = 'Informations';
        $this->views_folder = 'Administrator.information';
        $this->main_table = 'informations';
        $this->translates_table = 'informations_translates';
    }
    /*
     * ძირითადი ცხრილის ის ველები, რომელთა შესაბამისი html ელემენტების
     * ავტოდაგენერირებაც გვინდა წარმოდგენის ფაილში. ფაილური ველები და ის ველები,
     * რომლებიც შაბლონში ხელითაა აღწერილი, გამორიცხულია.
     */
    public function main_columns()
    {
        $main_table_columns = Schema::getColumnListing($this->main_table);
        $main_no_generate_columns = array_merge([
            'id',
            'sort',
            'status',
            'created_at',
            'updated_at',
            'longitude',
            'latitude',
            'pixel',
            'analytics',
        ], Information::$file_columns);
        return array_values(array_diff($main_table_columns, $main_no_generate_columns));
    }
    public function translate_columns()
    {
        $translates_table_columns = Schema::getColumnListing($this->translates_table);
        $translates_no_generate_columns = [
            'id',
            'parent_id',
            'lang',
            'created_at',
            'updated_at',
        ];
        return array_values(array_diff($translates_table_columns, $translates_no_generate_columns));
    }
    public function edit()
    {
        $item = Information::resolveRecord();
        if (! $item) {
            return redirect()->route('AdminMainPage')->with('error', true);
        }
        return view($this->views_folder.'.edit', $this->viewData($item));
    }
    /*
     * მარშრუტის {id} პარამეტრი არასავალდებულოა, ამიტომ ფორმა მუშაობს როგორც
     * .../informations/update/3, ისე .../informations/update მისამართზე.
     */
    public function update(Request $request, $id = null)
    {
        $item = Information::resolveRecord($id);
        if (! $item) {
            return redirect()->route('AdminMainPage')->with('error', true);
        }
        $this->validate($request, $this->rules(), $this->messages());
        $request->session()->flash('last_edited_lang', $request->last_edited_lang);
        if (! $this->model->updateItem($request, $item)) {
            $request->session()->flash('error', true);
            return redirect()->route('Edit'.$this->routes_suffix);
        }
        $request->session()->flash('success', true);
        if ($request->stay) {
            return redirect()->route('Edit'.$this->routes_suffix);
        }
        return redirect()->route('AdminMainPage');
    }
    private function rules()
    {
        /*
         * pixel და analytics წესები მასივის სახითაა ჩაწერილი, რადგან რეგულარული
         * გამოსახულება შეიცავს | სიმბოლოს, რომელსაც Laravel წესების გამყოფად აღიქვამს.
         */
        $rules = [
            'translates.'.$this->configuration->admin_lang.'.title' => 'required|string|max:255',
            'translates.*.slogan' => 'nullable|string|max:90',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'pixel' => ['nullable', 'regex:/^\d{6,20}$/'],
            'analytics' => ['nullable', 'regex:/^(G-[A-Za-z0-9]{4,15}|UA-\d{4,12}-\d{1,4}|GTM-[A-Za-z0-9]{4,15})$/'],
        ];
        foreach (Information::$file_columns as $column) {
            $rules[$column] = 'nullable|file|mimes:jpeg,jpg,png,webp,svg,ico';
        }
        return $rules;
    }
    private function messages()
    {
        return [
            'pixel.regex' => trans('admin.pixel_id_invalid'),
            'analytics.regex' => trans('admin.analytics_id_invalid'),
        ];
    }
    private function viewData($item)
    {
        $this->data['item'] = $item;
        $this->data['model'] = $this->model;
        $this->data['main_table'] = $this->main_table;
        $this->data['routes_suffix'] = $this->routes_suffix;
        $this->data['main_columns'] = $this->main_columns();
        $this->data['translate_columns'] = $this->translate_columns();
        $this->data['required_columns'] = $this->required_columns;
        $this->data['file_columns'] = Information::$file_columns;
        $this->data['translations'] = Information::translationsByLang($item->id);
        return $this->data;
    }
}
