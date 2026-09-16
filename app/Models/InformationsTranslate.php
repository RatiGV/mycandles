<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class InformationsTranslate extends Model
{
    protected $table = 'informations_translates';
    protected $guarded = ['id'];
    public function information(): BelongsTo
    {
        return $this->belongsTo(Information::class, 'parent_id', 'id');
    }
}
