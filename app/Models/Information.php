<?php
namespace App\Models;
use App\Traits\ActionLog;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;
class Information extends Model
{
    use ActionLog;
    /* საკონტაქტო ინფორმაციის ერთადერთი ჩანაწერის ნაგულისხმევი იდენტიფიკატორი */
    public const RECORD_ID = 3;
    /* ატვირთვების საქაღალდე public დირექტორიის მიმართ */
    public const UPLOAD_DIRECTORY = 'uploads/informations';
    protected $table = 'informations';
    protected $guarded = ['id'];
    /* ფაილური ველები - ყველა მათგანი ერთი და იმავე წესით მუშავდება */
    public static $file_columns = [
        'logo',
        'logo_for_admin',
        'favicon',
        'login_bg',
        'top_banner',
        'bottom_banner',
    ];
    private static $main_table = 'informations';
    private static $translates_table = 'informations_translates';
    public function translate(): HasOne
    {
        return $this->hasOne(InformationsTranslate::class, 'parent_id', 'id')->where('lang', locale());
    }
    public function translations(): HasMany
    {
        return $this->hasMany(InformationsTranslate::class, 'parent_id', 'id');
    }
    public static function get_required_lang()
    {
        return DB::table('configurations')->select('admin_lang')->first()->admin_lang;
    }
    /**
     * ერთადერთი ჩანაწერის მოძებნა.
     * თანმიმდევრობა: გადმოცემული id, ნაგულისხმევი id, ცხრილის პირველი ჩანაწერი.
     * ასე გვერდი აღარ ვარდება, თუ ჩანაწერის id შეიცვალა.
     */
    public static function resolveRecord($id = null)
    {
        $item = $id ? self::find($id) : null;
        if (! $item) {
            $item = self::find(self::RECORD_ID);
        }
        if (! $item) {
            $item = self::orderBy('id')->first();
        }
        return $item;
    }
    /* თარგმანები ენის კოდის მიხედვით დაჯგუფებული */
    public static function translationsByLang($id)
    {
        return InformationsTranslate::where('parent_id', $id)->get()->keyBy('lang');
    }
    /**
     * ჩანაწერის განახლება: ძირითადი ველები, ფაილები და თარგმანები.
     *
     * @return bool
     */
    public function updateItem($request, $item)
    {
        $table_columns = Schema::getColumnListing(self::$main_table);
        $skip_keys = array_merge(
            ['_token', '_method', 'id', 'status', 'translates', 'stay', 'last_edited_lang'],
            self::$file_columns
        );
        foreach ($request->except($skip_keys) as $key => $value) {
            if (in_array($key, $table_columns, true)) {
                $item->$key = $value;
            }
        }
        foreach (self::$file_columns as $column) {
            self::storeUploadedFile($request, $item, $column);
        }
        if (! $item->save()) {
            return false;
        }
        self::saveTranslations($item, (array) $request->input('translates', []));
        self::storeLog($item, __CLASS__, 'Update');
        return true;
    }
    /**
     * ერთი ფაილური ველის დამუშავება.
     * გზა იწერება public_path()-ით, რომ არ იყოს დამოკიდებული მიმდინარე საქაღალდეზე.
     */
    public static function storeUploadedFile($request, $item, $column)
    {
        if (! $request->hasFile($column)) {
            return;
        }
        $file = $request->file($column);
        if (! $file || ! $file->isValid()) {
            return;
        }
        $directory = public_path(self::UPLOAD_DIRECTORY);
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
        if (! is_dir($directory) || ! is_writable($directory)) {
            return;
        }
        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension();
        $file_name = mt_rand(11111, 99999).time().'.'.$extension;
        $file->move($directory, $file_name);
        $item->$column = '/'.self::UPLOAD_DIRECTORY.'/'.$file_name;
    }
    /**
     * თარგმანების შენახვა.
     * firstOrNew გამოიყენება იმისათვის, რომ ენა, რომელსაც ჯერ არ აქვს ჩანაწერი,
     * ავტომატურად შეიქმნას - ძველი კოდი ასეთ შემთხვევაში ფატალურ შეცდომას იძლეოდა.
     * ცარიელი ველი ივსება ადმინისტრირების ძირითადი ენის მნიშვნელობით.
     */
    public static function saveTranslations($item, array $translates)
    {
        if (! count($translates)) {
            return;
        }
        $columns = Schema::getColumnListing(self::$translates_table);
        $protected_columns = ['id', 'parent_id', 'lang', 'created_at', 'updated_at'];
        $required_lang = self::get_required_lang();
        $fallback = [];
        if (isset($translates[$required_lang]) && is_array($translates[$required_lang])) {
            $fallback = $translates[$required_lang];
        }
        foreach ($translates as $lang => $translation_data) {
            if (! is_array($translation_data)) {
                continue;
            }
            $translate = InformationsTranslate::firstOrNew([
                'parent_id' => $item->id,
                'lang' => $lang,
            ]);
            foreach ($translation_data as $key => $value) {
                if (! in_array($key, $columns, true) || in_array($key, $protected_columns, true)) {
                    continue;
                }
                $is_empty = $value === null || $value === '';
                $translate->$key = $is_empty ? ($fallback[$key] ?? null) : $value;
            }
            $translate->parent_id = $item->id;
            $translate->lang = $lang;
            $translate->save();
        }
    }
    /**
     * ჩანაწერი თარგმანთან ერთად.
     * თუ მოთხოვნილ ენაზე თარგმანი არ არსებობს, ბრუნდება ძირითადი ენის თარგმანი,
     * ხოლო თუ არც ის არსებობს - ჩანაწერი თარგმანის გარეშე (null-ის ნაცვლად).
     */
    public static function getItemInfo($id = 0, $local = '')
    {
        $info = self::joinedInfo($id, $local);
        if ($info) {
            return $info;
        }
        $required_lang = self::get_required_lang();
        if ($local !== $required_lang) {
            $info = self::joinedInfo($id, $required_lang);
            if ($info) {
                return $info;
            }
        }
        return self::find($id);
    }
    private static function joinedInfo($id, $local)
    {
        return self::join(self::$translates_table, self::$main_table.'.id', self::$translates_table.'.parent_id')
            ->where(self::$main_table.'.id', $id)
            ->where(self::$translates_table.'.lang', $local)
            ->select(
                self::$main_table.'.*',
                self::$translates_table.'.title',
                self::$translates_table.'.address',
                self::$translates_table.'.slogan'
            )
            ->first();
    }
}
