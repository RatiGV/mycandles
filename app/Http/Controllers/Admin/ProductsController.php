<?php

namespace App\Http\Controllers\Admin;

class ProductsController extends BaseController
{
    public function index()
    {
        return view('Administrator.products.index');
    }
}
