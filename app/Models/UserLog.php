<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_path',
        'model_name',
        'model_id',
        'admin_id',
        'ip_address',
        'action',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin::class);
    }

    public function getRelateModelUrl($id)
    {
        $model = static::find($id);
        $route = 'Edit'.$model->model_name.'s';

        if (substr($model->model_name, -1) == 'y') {
            $route = 'Edit'.substr_replace($model->model_name, '', -1).'ies';
        } elseif (substr($model->model_name, -1) == 's') {
            $route = 'Edit'.$model->model_name;
        }

        return str_replace(' ', '', $route);
    }
}
