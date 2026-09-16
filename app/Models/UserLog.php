<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
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
    /*
     * model_name ჩაწერის მომენტში ტექსტად ინახება, ამიტომ მისგან მარშრუტის
     * სახელის გამოცნობა საიმედო არაა: Faq-ს შეესაბამება EditFaq და არა EditFaqs.
     * ვაგროვებთ შესაძლო ვარიანტებს და ვირჩევთ იმას, რომელიც ნამდვილად არსებობს.
     */
    public function nameCandidates(): array
    {
        $name = str_replace(' ', '', (string) $this->model_name);
        if ($name === '') {
            return [];
        }
        $candidates = [$name];
        if (str_ends_with($name, 'y')) {
            $candidates[] = substr($name, 0, -1).'ies';
        } elseif (! str_ends_with($name, 's')) {
            $candidates[] = $name.'s';
        }
        return array_values(array_unique($candidates));
    }
    public function relatedRouteName(): ?string
    {
        foreach ($this->nameCandidates() as $candidate) {
            if (Route::has('Edit'.$candidate)) {
                return 'Edit'.$candidate;
            }
        }
        return null;
    }
    /*
     * null ბრუნდება, როცა შესაბამისი მარშრუტი არ არსებობს - მაგალითად ისეთი
     * ჩანაწერისთვის, რომლის მოდელიც წაშლილია. შაბლონი ასეთ დროს ბმულს არ ხატავს.
     */
    public function relatedModelUrl(): ?string
    {
        $name = $this->relatedRouteName();
        if (! $name) {
            return null;
        }
        $route = Route::getRoutes()->getByName($name);
        if ($route && count($route->parameterNames())) {
            return route($name, $this->model_id);
        }
        return route($name);
    }
    public function relatedModelLabel(): string
    {
        foreach ($this->nameCandidates() as $candidate) {
            if (Lang::has('admin.routes.'.$candidate)) {
                return trans('admin.routes.'.$candidate);
            }
        }
        return (string) $this->model_name;
    }
}
