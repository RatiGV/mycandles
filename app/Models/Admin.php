<?php

namespace App\Models;

use App\Traits\ActionLog;
use Illuminate\Database\Eloquent\Model;
use Session;

class Admin extends Model
{
    use ActionLog;

    protected $fillable = ['name', 'surname', 'password', 'email', 'role'];

    public static function isLogin()
    {
        if (! Session::has('admin')) {
            return false;
        }

        $idAdmin = Session::get('admin');
        $AdminExist = Admin::find($idAdmin);

        if (! $AdminExist) {
            return false;
        }

        return true;
    }

    public static function getInfo()
    {
        $idAdmin = Session::get('admin');
        $AdminExist = Admin::find($idAdmin);

        return $AdminExist;
    }

    public static function addAdmin($request)
    {
        $NewAdmin = new Admin();
        $NewAdmin->name = $request->name;
        $NewAdmin->role = $request->role;
        $NewAdmin->surname = $request->surname;
        $NewAdmin->email = $request->email;
        $NewAdmin->password = sha1($request->password);

        if ($NewAdmin->save()) {
            $NewAdmin::storeLog($NewAdmin, __CLASS__, 'Create');

            return true;
        }

        return false;
    }

    public static function UpdateAdmin($request, $admin)
    {
        if ($request->password) {
            $update = $admin->update([
                'name' => $request->name,
                'surname' => $request->surname,
                'email' => $request->email,
                'password' => sha1($request->password),
                'role' => $request->role ? $request->role : 1,
            ]);
        } else {
            $update = $admin->update([
                'name' => $request->name,
                'surname' => $request->surname,
                'email' => $request->email,
                'role' => $request->role ? $request->role : 1,
            ]);
        }

        if ($update) {
            $admin::storeLog($admin, __CLASS__, 'Update');

            return true;
        }

        return false;
    }

    public static function GetAll()
    {
        return Admin::all();
    }
}
