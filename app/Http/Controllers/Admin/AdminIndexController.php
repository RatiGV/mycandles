<?php

namespace App\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;

class AdminIndexController extends BaseController
{
    public function index(Request $request)
    {
        /*********************** /ჯამური თანხები დღეების მიხედვით ********************/
        if ($request->has('year_month')) {
            $year1 = explode('-', $request->get('year_month'))[0];
            $month1 = explode('-', $request->get('year_month'))[1];
        } else {
            $year1 = date('Y');
            $month1 = date('m');
        }

        $data1 = [];
        $data1_formated = [];

        $orders1 = DB::table('orders')->select('day', DB::raw('SUM(total) as total'))
            ->where('year', $year1)->where('month', $month1)->groupBy('day')->get();

        foreach ($orders1 as $order1) {
            $data1_formated[$order1->day] = $order1->total;
        }

        if ($orders1->count()) {
            for ($i = 1; $i < 32; $i++) {
                if (array_key_exists($i, $data1_formated)) {
                    $data1[] = [
                        'label' => strftime('%d %b', strtotime(date('Y-'.$month1.'-'.$i))),
                        'y' => $data1_formated[$i],
                    ];
                } else {
                    $data1[] = [
                        'label' => strftime('%d %b', strtotime(date('Y-'.$month1.'-'.$i))),
                        'y' => 0,
                    ];
                }
            }
        }

        /*********************** /ჯამური თანხები დღეების მიხედვით ********************/

        /*********************** ჯამური თანხები თვეების მიხედვით ********************/

        if ($request->has('year')) {
            $year2 = $request->get('year');
        } else {
            $year2 = date('Y');
        }

        $data2 = [];
        $data2_formated = [];

        $orders2 = DB::table('orders')->select('month', DB::raw('SUM(total) as total'))
            ->where('year', $year2)->groupBy('month')->get();

        foreach ($orders2 as $order2) {
            $data2_formated[$order2->month] = $order2->total;
        }

        if ($orders2->count()) {
            for ($j = 1; $j < 13; $j++) {
                if (array_key_exists($j, $data2_formated)) {
                    $data2[] = [
                        'label' => strftime('%B', strtotime(date('Y-'.$j.'-01'))),
                        'y' => $data2_formated[$j],
                    ];
                } else {
                    if ($j < date('m')) {
                        $data2[] = [
                            'label' => strftime('%B', strtotime(date('Y-'.$j.'-01'))),
                            'y' => 0,
                        ];
                    }
                }
            }
        }

        /*********************** /ჯამური თანხები თვეების მიხედვით ********************/

        /*********************** ტოპ 10 გაყიდვადი პროდუქტი ********************/

        $data3 = [];
        $products = DB::select("SELECT 
                                    products_translates.title,
                                    IFNULL(B.total_qty,0) AS qty,
                                    IFNULL(B.total_price,0) AS price 
                                 FROM products_translates 
                                 RIGHT JOIN (SELECT product_id, SUM(qty) AS total_qty, SUM(price) AS total_price FROM order_products GROUP BY product_id) AS B
                                 ON products_translates.parent_id=B.product_id 
                                 WHERE products_translates.lang='".$this->configuration->admin_lang."'
                                 ORDER BY B.total_qty DESC LIMIT 10");

        if (count($products)) {
            foreach ($products as $product) {
                $data3[] = [
                    'label' => $product->title,
                    'y' => (int) $product->qty,
                ];
            }
        }

        /*********************** ტოპ 10 გაყიდვადი პროდუქტი ********************/

        /*********************** შეკვეთების რაოდენობა უბნების მიხედვით ********************/

        $data4 = [];
        $data5 = [];
        $sup_total_count = 0; // შეკვეთების მთლიანი რაოდენობა
        $sup_total_amount = 0; // შეკვეთების თანხების მთლიანი ჯამი

        $districts = DB::select("SELECT 
                                    districts_translates.title,
                                    IFNULL(B.orders_count,0) AS orders_total_count,
                                    IFNULL(B.total_amount,0) AS orders_total_amount
                                  FROM districts_translates 
                                  RIGHT JOIN (SELECT district_id, count(id) AS orders_count, SUM(total) AS total_amount FROM orders GROUP BY district_id) AS B
                                  ON districts_translates.parent_id=B.district_id 
                                  WHERE districts_translates.lang='".$this->configuration->admin_lang."'");

        // ეს შესასწორებელია : SQL ბრძანებაშივე უნდა ამოვიღო ჯამური თანხა და ჯამური რაოდენობა
        foreach ($districts as $district) {
            $sup_total_count += $district->orders_total_count;
            $sup_total_amount += $district->orders_total_amount;
        }

        foreach ($districts as $district) {
            $data4[] = [
                'label' => $district->title.' ('.$district->orders_total_count.' '.trans('admin.order').')',
                'y' => (100 * $district->orders_total_count) / $sup_total_count,
            ];

            $data5[] = [
                'label' => $district->title.' ('.$district->orders_total_amount.' ₾)',
                'y' => (100 * $district->orders_total_amount) / $sup_total_amount,
            ];
        }

        /*********************** /შეკვეთების რაოდენობა უბნების მიხედვით ********************/

        return view('Administrator.index.index', compact('data1', 'data2', 'data3', 'data4', 'data5', 'sup_total_count', 'sup_total_amount'));
    }
}
