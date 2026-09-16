<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrdersExport;
use App\Models\Information;
use App\Models\Order;
use App\Models\User;
use DB;
use Excel;
use Illuminate\Http\Request;
use PDF;

class OrdersController extends BaseController
{
    public $data = [];

    public function index(Request $request)
    {
        $orders = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->whereNotNull('orders.id');

        if ($request->has('pay_status') && $request->get('pay_status') != 1) {
            if ($request->get('pay_status') == 2) {
                $orders->where('pay_status', 4);
            } else {
                $orders->where('pay_status', '<>', 4);
            }
        }

        if ($request->has('done') && $request->get('done') != 1) {
            if ($request->get('done') == 2) {
                $orders->where('done', 1);
            } else {
                $orders->where('done', 0);
            }
        }

        if ($request->code) {
            $orders->where('code', 'like', "%$request->code%");
        }

        if ($request->user_id) {
            $orders->where('user_id', $request->user_id);
        }

        if ($request->total_from) {
            $orders->where('total', '>=', $request->total_from);
        }

        if ($request->total_to) {
            $orders->where('total', '<=', $request->total_to);
        }

        if ($request->payment_type) {
            $orders->where('payment_type', $request->payment_type);
        }

        if ($request->from) {
            $orders->where('.orders.created_at', '>', $request->from);
        }

        if ($request->to) {
            $orders->where('orders.created_at', '<', $request->to);
        }

        $this->data['orders'] = $orders->select('orders.*', 'users.name')
            ->orderby('orders.id', 'DESC')->paginate(10);

        $this->data['users'] = User::select(['id', 'name'])
            ->orderBy('name')->get();

        return view('Administrator.orders.index', $this->data);
    }

    public function details($id)
    {
        $order = Order::findOrFail($id);
        $this->data['order'] = $order;
        $this->data['user'] = DB::table('users')->find($order->user_id);

        return view('Administrator.orders.details', $this->data);
    }

    public function status(Request $request)
    {
        $id = $request->id;
        $item = Order::find($id);

        if (! $item) {
            return response()->json(['status' => 0]);
        }

        if ($item->payment_type == 1) {
            return response()->json(['status' => 0]);
        }

        $item->pay_status = $item->pay_status == 0 ? 4 : 0;

        if (! $item->update()) {
            return response()->json(['status' => 0]);
        }

        return response()->json(['status' => 1]);
    }

    public function export(Request $request)
    {
        $orders = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->whereNotNull('orders.id');
        if ($request->has('pay_status') && $request->get('pay_status') != 1) {
            if ($request->get('pay_status') == 2) {
                $orders->where('pay_status', 4);
            } else {
                $orders->where('pay_status', '<>', 4);
            }
        }

        if ($request->has('done') && $request->get('done') != 1) {
            if ($request->get('done') == 2) {
                $orders->where('done', 1);
            } else {
                $orders->where('done', 0);
            }
        }

        if ($request->code) {
            $orders->where('code', 'like', "%$request->code%");
        }

        if ($request->user_id) {
            $orders->where('user_id', $request->user_id);
        }

        if ($request->total_from) {
            $orders->where('total', '>=', $request->total_from);
        }

        if ($request->total_to) {
            $orders->where('total', '<=', $request->total_to);
        }

        if ($request->payment_type) {
            $orders->where('payment_type', $request->payment_type);
        }

        if ($request->from) {
            $orders->where('orders.created_at', '>', $request->from);
        }

        if ($request->to) {
            $orders->where('orders.created_at', '<', $request->to);
        }
        $orders->select('orders.code', 'orders.address', 'users.first_name', 'users.last_name', 'orders.total')->orderby('orders.id', 'DESC');

        // $fileName = "members-data_" . date('Y-m-d') . ".xlsx";

        // // Column names
        // $fields = array('code', 'Address','first name', 'last name','total');
        // // Display column names as first row
        // $excelData = implode("\t", array_values($fields)) . "\n";

        // foreach($orders->get() as $data){
        //     $lineData = array($data['code'], $data['address'],$data['first_name'], $data['last_name'],$data['total']);
        //     $excelData .= implode("\t", array_values($lineData)) . "\n";
        // }
        // header("Content-Type: application/vnd.ms-excel");
        // header("Content-Disposition: attachment; filename=\"$fileName\"");

        // return $excelData;
        return Excel::download(new OrdersExport($request), 'შეკვეთები.xlsx');
    }

    public function generate_pdf($id)
    {
        $total = 0;
        $order = Order::findOrFail($id);

        $invoice_data = [
            'order' => $order,
            'user' => User::find($order->user_id),
            'contact_info' => Information::getItemInfo(3, $this->configuration->admin_lang),
        ];

        $pdf = new PDF;

        $pdf = PDF::loadView('Administrator.orders.invoice', $invoice_data, ['format' => 'A4-P']);
        //$pdf->save('../public_html/uploads/invoices/'.$order_code.'.pdf');
        //return $pdf->download($order->code.'.pdf');

        return $pdf->stream();
    }
}
