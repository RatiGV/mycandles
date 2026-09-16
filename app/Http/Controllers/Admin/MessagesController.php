<?php

namespace App\Http\Controllers\Admin;

use App\Models\Message;
use Illuminate\Http\Request;
use Session;

class MessagesController extends BaseController
{
    public $data = []; // წარმოდგენის ფაილებზე მისამაგრებელი ინფორმაცია

    private $model; // მიმდინარე ინსტანციის მოდელი

    private $views_folder; // წარმოდგენების ფაილების საქაღალდე  მიმდინარე ინსტანციისათვის

    private $main_table; // მიმდინარე ინსტანციის ძირითადი ცხრილი

    /*
    * მარშრუტების სუფიქსი მიმდინარე ინსტანციისათვის, გამოიყენება ბმულების
    * გენერირებისათვის კონტროლერებსა და წარმოდგენის ფაილებში. ძირითადი პრეფიქსები,
    * რომლებიც დაერთვის: 'Add', 'Store', 'Edit', 'Update', 'Remove', 'Status', 'Ordering'
    */
    private $routes_suffix;

    public function __construct(Message $model)
    {
        parent::__construct();

        $this->model = $model;
        $this->routes_suffix = 'Messages';
        $this->views_folder = 'Administrator.messages';
        $this->main_table = 'messages';
    }

    public function index()
    {
        $this->data['items'] = $this->model->allItems();

        return view($this->views_folder.'.index', $this->data);
    }

    public function remove($id)
    {
        $item = $this->model::find($id);

        if (! $item) {
            return redirect()->back();
        }

        $item->delete();
        Session::flash('success', true);

        return redirect()->back();
    }

    public function seen(Request $request)
    {
        $id = $request->id;
        $item = $this->model::find($id);

        if (! $item || $item->seen) {
            return response()->json(['status' => 0]);
        }

        $item->seen = 1;

        if (! $item->update()) {
            return response()->json(['status' => 0]);
        }

        return response()->json(['status' => 1]);
    }
}
