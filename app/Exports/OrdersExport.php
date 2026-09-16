<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $orders = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->whereNotNull('orders.id');

        if ($this->request->has('pay_status') && $this->request->get('pay_status') != 1) {
            if ($this->request->get('pay_status') == 2) {
                $orders->where('pay_status', 4);
            } else {
                $orders->where('pay_status', '<>', 4);
            }
        }

        if ($this->request->has('done') && $this->request->get('done') != 1) {
            if ($this->request->get('done') == 2) {
                $orders->where('done', 1);
            } else {
                $orders->where('done', 0);
            }
        }

        if ($this->request->code) {
            $orders->where('code', 'like', "%$this->request->code%");
        }

        if ($this->request->user_id) {
            $orders->where('user_id', $this->request->user_id);
        }

        if ($this->request->total_from) {
            $orders->where('total', '>=', $this->request->total_from);
        }

        if ($this->request->total_to) {
            $orders->where('total', '<=', $this->request->total_to);
        }

        if ($this->request->payment_type) {
            $orders->where('payment_type', $this->request->payment_type);
        }

        if ($this->request->from) {
            $orders->where('orders.created_at', '>', $this->request->from);
        }

        if ($this->request->to) {
            $orders->where('orders.created_at', '<', $this->request->to);
        }

        return $orders->select('orders.code', 'orders.address', 'users.first_name', 'users.last_name', 'orders.total')
            ->orderby('orders.id', 'DESC')->get();

        /*
        return $orders = Order::join('users', 'orders.user_id', '=', 'users.id')
                ->join('districts_translates', 'orders.district_id', '=', 'districts_translates.parent_id')
                ->select(
                            'code',
                            'address',
                            'districts_translates.title AS district_title',
                            'users.first_name',
                            'users.last_name',
                            'orders.total'
                        )
                ->where('districts_translates.lang','=','ka')
                ->get();
         *
         */

    }

    public function headings(): array
    {
        return [
            trans('admin.code'),
            trans('admin.address'),
            trans('admin.first_name'),
            trans('admin.last_name'),
            trans('admin.total'),
        ];
    }
}
