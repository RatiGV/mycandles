<?php

namespace App\Http\Controllers\Admin;

use App\Models\UserLog;

class ActionLogController extends BaseController
{
    public $data = [];

    public function index()
    {
        $this->data['main_table'] = 'user_logs';
        $this->data['items'] = UserLog::with('admin')->orderBy('id', 'DESC')->paginate(15);

        return view('Administrator.logs.actionlogs.index', $this->data);
    }
}
