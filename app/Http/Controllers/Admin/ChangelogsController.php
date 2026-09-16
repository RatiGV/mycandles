<?php

namespace App\Http\Controllers\Admin;

use DB;

class ChangelogsController extends BaseController
{
    public $data = [];

    public function index()
    {
        $this->data['main_table'] = 'changelogs';
        $this->data['items'] = DB::table('changelogs')->orderBy('id', 'DESC')->paginate(15);

        return view('Administrator.logs.changelogs.index', $this->data);
    }
}
