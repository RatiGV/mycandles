<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Faq;

class ContactController extends Controller
{
    public $data = [];

    public function index()
    {
        $this->data['info'] = information();

        return view('client.contact.index', $this->data);
    }

    public function faq() {

        $this->data['faqs'] = Faq::allItems(locale(),true);

        return view('client.faq', $this->data);
    }
}
