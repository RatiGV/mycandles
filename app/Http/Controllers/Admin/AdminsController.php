<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;

class AdminsController extends BaseController
{
    public $data = [];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->data['admins'] = Admin::GetAll();

        return view('Administrator.admins.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Administrator.admins.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'surname' => 'required',
            'password' => 'required',
            'email' => 'required|email|unique:admins',
            'role' => 'required|numeric|min:2|max:3',
        ]);

        $InsertAdmin = Admin::addAdmin($request);

        if (! $InsertAdmin) {
            $request->session()->flash('error', true);

            return redirect()->route('Admins');
        }

        $request->session()->flash('success', true);

        return redirect()->route('Admins');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin = Admin::find($id);

        if (! $admin) {
            return redirect()->back();
        }

        $this->data['admin'] = $admin;

        return view('Administrator.admins.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $count = Admin::where('id', '!=', $id)->where('email', $request->email)->count();

        if ($count) {
            return redirect()->route('Admins');
        }

        $admin = Admin::find($id);

        if (! $admin) {
            return redirect()->back();
        }

        if ($admin->role == 1) {
            $this->validate($request, [
                'name' => 'required',
                'surname' => 'required',
                'email' => 'required|email',
            ]);
        } else {
            $this->validate($request, [
                'name' => 'required',
                'surname' => 'required',
                'email' => 'required|email',
                'role' => 'required|numeric|min:2|max:3',
            ]);
        }

        $updateAdmin = Admin::UpdateAdmin($request, $admin);

        if (! $updateAdmin) {
            $request->session()->flash('error', true);

            return redirect()->route('EditAdmins', $id);
        }

        $request->session()->flash('success', true);

        if ($request->stay) {
            return redirect()->route('EditAdmins', $id);
        } else {
            return redirect()->route('Admins');
        }
    }

    public function remove(Request $request)
    {
        $id = $request->id;
        $admin = Admin::find($id);

        if (! $admin || $admin->role === 1) {
            return response()->json(['status' => 0]);
        }

        if (! $admin->delete()) {
            return response()->json(['status' => 0]);
        }

        return response()->json(['status' => 1]);
    }
}
