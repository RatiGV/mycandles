<?php

namespace App\Traits;

use App\Models\UserLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

trait ActionLog
{
    public static function bootModelLog()
    {
        static::saved(function ($model) {
            if ($model->wasRecentlyCreated) {
                static::storeLog($model, static::class, 'CREATED');
            } else {
                if (! $model->getChanges()) {
                    return;
                }
                static::storeLog($model, static::class, 'UPDATED');
            }
        });

        static::deleted(function (Model $model) {
            static::storeLog($model, static::class, 'DELETED');
        });
    }

    public static function getTagName(Model $model)
    {
        return ! empty($model->tagName) ? $model->tagName : Str::title(Str::snake(class_basename($model), ' '));
    }

    public static function activeAdminId()
    {
        if (Session::has('admin')) {
            return Session::get('admin');
        }

        return false;
    }

    public static function storeLog($model, $modelPath, $action)
    {
        $data = [
            'model_path' => $modelPath,
            'model_name' => static::getTagName($model),
            'model_id' => $model->id,
            'admin_id' => static::activeAdminId()->id,
            'ip_address' => \Request::ip(),
            'action' => $action,
        ];

        UserLog::create($data);
    }

    public static function uploadFile($request, $data)
    {
        $file = $request->file($data['column_name']);
        $filename = mt_rand(10000, 99999) . time() . '.' . $file->getClientOriginalExtension();
        $uploadPath = public_path('uploads/' . $data['upload_folder']);
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        $file->move($uploadPath, $filename);
        $data['item']->{$data['column_name']} = '/uploads/' . $data['upload_folder'] . '/' . $filename;
        return true;
    }

    public static function uploadGalleryImage($request, $file, $item, $class_base_name, $gallery_table)
    {
        $filename = mt_rand(10000, 99999) . time() . '.' . $file->getClientOriginalExtension();
        $uploadPath = public_path('uploads/' . $class_base_name . '/gallery');
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        $file->move($uploadPath, $filename);
        DB::table($gallery_table)->insert([
            'parent_id' => $item->id,
            'image' => '/uploads/' . $class_base_name . '/gallery/' . $filename,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return true;
    }
}
