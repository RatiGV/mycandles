<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Information;
use DB;
use Illuminate\Http\Request;
use Session;

class LoginController extends BaseController
{
    public function index()
    {
        $information = Information::getItemInfo(3, $this->configuration->admin_lang);

        return view('Administrator.login.index', compact('information'));
    }

    public function singin(Request $request)
    {
        $this->validate($request, [
            'password' => 'required',
            'email' => 'required|email',
        ]);

        $admin = Admin::where('email', $request->email)->where('password', sha1($request->password))->first();

        if (! $admin) {
            return redirect()->back();
        }

        $admin->last_login = date('Y-m-d H:i:s');
        $admin->update();

        Session::put('admin', $admin);

        DB::table('changelogs')->insert([
            'user' => $admin->name.' '.$admin->surname,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $request->header('User-Agent'),
            'time' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('AdminMainPage');
    }

    public function logout(Request $request)
    {
        if (Admin::isLogin()) {
            Session::forget('admin');

            return response()->json(['status' => 1]);
        }

        return response()->json(['status' => 0]);
    }
}
