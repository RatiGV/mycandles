<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Information;
use App\Models\ProductCategory;
use DB;
use Illuminate\Http\Request;
use Lang;
use LaravelLocalization;
use View;

class BaseController extends Controller
{
    public $configuration;

    public function __construct()
    {
        $this->configuration = DB::table('configurations')->first();
        Lang::setLocale($this->configuration->admin_lang);
        View::share('configuration', $this->configuration);
        View::share('Localization', $this->get_languages());
        View::share('information', Information::getItemInfo(3, $this->configuration->admin_lang));
    }

    /**
     * integer ტიპის ისეთი ველების განახლება, რომელთა შესაძლო მნიშვნელობებიცაა 0 და 1
     *
     * @param  $request  მომხმარებლის მიერ გაგზავნილი მოთხოვნის აბსტრაქციის - Request კლასის ფსევდონიმი.
     * @return status : 1 => წარმატება,  2 => შეცდომა, 5 => უფლება არ აქვს
     */
    protected function status(Request $request)
    {
        $id = $request->id;
        $table = $request->table;
        $column = $request->column;
        $item = DB::table($table)->find($id);

        if (! $item) {
            return response()->json([
                'status' => 2,
                'type' => 'error',
                'text' => trans('admin.error'),
            ]);
        }

        $affected = DB::table($table)->where('id', $id)->update([$column => $item->$column ? 0 : 1]);

        if (! $affected) {
            return response()->json([
                'status' => 2,
                'type' => 'error',
                'text' => trans('admin.error'),
            ]);
        }

        return response()->json([
            'status' => 1,
            'type' => 'success',
            'text' => trans('admin.success'),
        ]);
    }

    /**
     * სტატუსის შეცვლა ან წაშლა რამოდენიმე ჩანაწერზე ერთდროულად
     *
     * @param  $request  მომხმარებლის მიერ გაგზავნილი მოთხოვნის აბსტრაქციის - Request კლასის ფსევდონიმი.
     * @return void
     */
    protected function multi(Request $request)
    {
        if (! $request->id || ! count($request->id)) {
            return redirect()->back();
        }

        $cant_remove = [];

        foreach ($request->id as $id) {
            $item = DB::table($request->table)->find($id);

            // წაშლა
            if ($request->action == 1) {
                if (count(json_decode($request->check_childs_here, true))) {
                    foreach (json_decode($request->check_childs_here, true) as $table => $column) {
                        if (DB::table($table)->where($column, $id)->count()) {
                            array_push($cant_remove, $id);
                        }
                    }
                }

                if (! in_array($id, $cant_remove)) {
                    if (property_exists($item, 'image')) {
                        if (file_exists('../public_html'.$item->image)) {
                            unlink('../public_html'.$item->image);
                        }

                        /*
                        // როდესაც მთლიანი საიტი public_html საქაღალდეშია
                        if(file_exists(public_path($item->image)))
                        {
                            unlink(public_path($item->image));
                        }
                         *
                         */
                    }

                    DB::table($request->table)->where('id', $id)->delete();
                }

                if (count($cant_remove)) {
                    $request->session()->flash('cant_remove', $cant_remove);
                }
            } else { // სტატუსის შეცვლა
                DB::table($request->table)->where('id', $id)->update(['status' => $item->status ? 0 : 1]);
            }
        }

        if (count($cant_remove) !== count($request->id)) {
            $request->session()->flash('success', true);
        }

        return redirect()->back();
    }

    /**
     * ჩანაწერების წაშლა
     *
     * @param  $request  მომხმარებლის მიერ გაგზავნილი მოთხოვნის აბსტრაქციის - Request კლასის ფსევდონიმი.
     * @return status : 1 => წარმატება,  2 => შეცდომა, 5 => უფლება არ აქვს
     */
    protected function remove(Request $request)
    {
        $id = $request->id;
        $table = $request->table;
        $item = DB::table($table)->find($id);

        if (! $item) {
            return response()->json([
                'status' => 2,
                'type' => 'error',
                'text' => trans('admin.error'),
            ]);
        }

        $childs = 0;

        if ($request->check_childs_here && count($request->check_childs_here)) {
            foreach ($request->check_childs_here as $tbl => $column) {
                if (DB::table($tbl)->where($column, $id)->count()) {
                    $childs++;
                }
            }
        }

        if ($childs) {
            return response()->json([
                'status' => 5,
                'type' => 'error',
                'text' => trans('admin.childs_error'),
            ]);
        }

        if (property_exists($item, 'image')) {
            if (file_exists('../public_html'.$item->image)) {
                unlink('../public_html'.$item->image);
            }

            /*
            // როდესაც მთლიანი საიტი public_html საქაღალდეშია
            if(file_exists(public_path($item->image)))
            {
                unlink(public_path($item->image));
            }
             *
             */
        }

        $delete = DB::table($table)->where('id', $id)->delete();

        if (! $delete) {
            return response()->json([
                'status' => 2,
                'type' => 'error',
                'text' => trans('admin.error'),
            ]);
        }

        return response()->json([
            'status' => 1,
            'type' => 'success',
            'text' => trans('admin.success'),
        ]);
    }

    /**
     * თანმიმდევრობის შეცვლა ჩამონათვალის გვერდებზე
     *
     * @param  $request  მომხმარებლის მიერ გაგზავნილი მოთხოვნის აბსტრაქციის - Request კლასის ფსევდონიმი.
     * @return status : 1 => წარმატება,  2 => შეცდომა, 5 => უფლება არ აქვს
     */
    protected function ordering(Request $request)
    {
        $orders = json_decode($request->ordering);

        foreach ($orders as $value) {
            $item = DB::table($request->table)->find($value[0]);
            //$item = $model->find($value[0]);

            if ($item) {
                $update = DB::table($request->table)->where('id', $value[0])->update(['sort' => $value[1]]);
                //$item->sort = $value[1];
            }

            /*
            if(!$update)
            {
                return response()->json([
                    'status' => 2,
                    'type' => 'error',
                    'text' => trans('admin.error')
                ]);
            }
             *
             */
        }

        return response()->json([
            'status' => 1,
            'type' => 'success',
            'text' => trans('admin.success'),
        ]);
    }

    /**
     * მიმაგრებული ფაილის წაშლა
     *
     * @param  $request  მომხმარებლის მიერ გაგზავნილი მოთხოვნის აბსტრაქციის - Request კლასის ფსევდონიმი.
     * @return status : 1 => წარმატება,  2 => შეცდომა, 5 => უფლება არ აქვს
     */
    protected function remove_file(Request $request)
    {
        $id = $request->id;
        $column = $request->column;
        $table = $request->table;
        $item = DB::table($table)->find($id);

        if (! $item) {
            return response()->json([
                'status' => 2,
                'type' => 'error',
                'text' => trans('admin.error'),
            ]);
        }

        $update = DB::table($table)->where('id', $id)->update([$column => null]);

        if (! $update) {
            return response()->json([
                'status' => 2,
                'type' => 'error',
                'text' => trans('admin.error'),
            ]);
        }

        if ($item->$column && file_exists(public_path($item->$column))) {
            unlink(public_path($item->$column));
        }

        return response()->json([
            'status' => 1,
            'type' => 'success',
            'text' => trans('admin.success'),
        ]);
    }

    /**
     * ფოტოს წაშლა გალერიიდან
     *
     * @param  $request  მომხმარებლის მიერ გაგზავნილი მოთხოვნის აბსტრაქციის - Request კლასის ფსევდონიმი.
     * @return status : 1 => წარმატება,  2 => შეცდომა, 5 => უფლება არ აქვს
     */
    protected function remove_image_from_gallery(Request $request)
    {
        $image_id = $request->image_id;
        $gallery_table = $request->gallery_table;
        $column = 'image';
        $item = DB::table($gallery_table)->find($image_id);

        if (! $item) {
            return response()->json([
                'status' => 2,
                'type' => 'error',
                'text' => trans('admin.error'),
            ]);
        }

        if ($item->$column && file_exists(public_path($item->$column))) {
            unlink(public_path($item->$column));
        }

        $delete = DB::table($gallery_table)->where('id', $image_id)->delete();

        if (! $delete) {
            return response()->json([
                'status' => 2,
                'type' => 'error',
                'text' => trans('admin.error'),
            ]);
        }

        return response()->json([
            'status' => 1,
            'type' => 'success',
            'text' => trans('admin.success'),
        ]);
    }

    /**
     * ვიდეოს წაშლა გალერიიდან
     *
     * @param  $request  მომხმარებლის მიერ გაგზავნილი მოთხოვნის აბსტრაქციის - Request კლასის ფსევდონიმი.
     * @return status : 1 => წარმატება,  2 => შეცდომა, 5 => უფლება არ აქვს
     */
    protected function remove_video_from_gallery(Request $request)
    {
        $video_id = $request->video_id;
        $gallery_table = $request->gallery_table;
        $item = DB::table($gallery_table)->find($video_id);

        if (! $item) {
            return response()->json([
                'status' => 2,
                'type' => 'error',
                'text' => trans('admin.error'),
            ]);
        }

        $delete = DB::table($gallery_table)->where('id', $video_id)->delete();

        if (! $delete) {
            return response()->json([
                'status' => 2,
                'type' => 'error',
                'text' => trans('admin.error'),
            ]);
        }

        return response()->json([
            'status' => 1,
            'type' => 'success',
            'text' => trans('admin.success'),
        ]);
    }

    public function get_categories()
    {
        $categories = ProductCategory::allItems($this->configuration->admin_lang, $level = false, $status_on = true);

        if ($categories->count()) {
            $max_level = $categories->sortBy('level')->last()->level;

            for ($level = $max_level; $level > 0; $level--) {
                $current_level_cats = $categories->where('level', $level);
                $next_level_cats = $categories->where('level', $level + 1);

                foreach ($current_level_cats as $current_level_cat) {
                    $current_level_cat->childs = $next_level_cats->where('parent_id', $current_level_cat->id);
                }
            }
        }

        return $categories->where('level', 1);
    }

    protected function get_languages()
    {
        $this->localArray = LaravelLocalization::getSupportedLocales();
        $result = [];
        $Key = 0;

        foreach ($this->localArray as $prefix => $array) {
            $result[$Key]['prefix'] = $prefix;
            $result[$Key]['name'] = $array['native'];
            $Key++;
        }

        return $result;
    }
}
