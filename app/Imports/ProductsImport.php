<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductsTranslate;
use Illuminate\Support\Collection;
use LaravelLocalization;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $rows = $rows->toArray();

        if (count($rows)) {
            if (array_keys($rows[0]) !== ['code', 'title', 'old_price', 'new_price']) {
                return redirect()->back();
            }

            foreach ($rows as $row) {
                $p = Product::updateOrCreate(
                    ['code' => $row['code']],
                    [
                        'code' => $row['code'],
                        'price' => $row['new_price'],
                        'old_price' => $row['old_price'],
                        'new' => 0,
                        'available' => 0,
                        'status' => 0,
                    ]
                );

                foreach ($this->get_languages() as $lang) {
                    ProductsTranslate::updateOrCreate(
                        ['parent_id' => $p->id, 'lang' => $lang['prefix']],
                        [
                            'parent_id' => $p->id,
                            'title' => $row['title'],
                            'lang' => $lang['prefix'],
                        ]
                    );
                }
            }
        }
    }

    protected function get_languages()
    {
        $this->localArray = LaravelLocalization::getSupportedLocales();
        $result = [];
        $Key = 0;

        foreach ($this->localArray as $prefix => $array) {
            $result[$Key]['prefix'] = $prefix;
            $result[$Key]['name'] = $array['native'];
            $Key++;
        }

        return $result;
    }
}
