<?php

namespace App\Http\Controllers\Admin;

class NewsesController extends BaseController
{
    public function index()
    {
        return view('Administrator.newses.index');
    }
}
