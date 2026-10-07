<?php

use Illuminate\Support\Facades\Facade;

return [

    'locale' => 'ka',

    'fallback_locale' => 'ka',

    'faker_locale' => env('APP_FAKER_LOCALE', 'ka_GE'),

    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
        'PDF' => Barryvdh\DomPDF\Facade::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'Debugbar' => Barryvdh\Debugbar\Facade::class,
        'Image' => 'Intervention\Image\Facades\Image',
    ])->toArray(),

];
