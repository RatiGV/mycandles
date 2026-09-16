<?php

namespace App\Http\Controllers\Client;

use App\Models\Benefit;
use App\Models\Textpage;
use App\Http\Controllers\Controller;

class AboutController extends Controller
{
    public $data = [];

    public function index()
    {
        $this->data['about'] = Textpage::getItemInfo(1, locale());

        $this->data['benefits'] = Benefit::allItems(locale(), true);

        return view('client.about.index', $this->data);
    }

    public function terms()
    {
        $this->data['terms'] = Textpage::getItemInfo(2, locale());

        $this->data['benefits'] = Benefit::allItems(locale(), true);

        return view('client.terms-and-conditions', $this->data);
    }
}
