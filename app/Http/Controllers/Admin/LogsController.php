<?php

namespace App\Http\Controllers\Admin;

class LogsController extends BaseController
{
    public function index()
    {
        return view('Administrator.logs.index');
    }
}
