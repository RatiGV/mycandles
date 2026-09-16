<?php

namespace App\Imports;

use App\Models\Coupon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CouponsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $rows = $rows->toArray();

        if (count($rows)) {
            if (array_keys($rows[0]) !== ['code', 'percent']) {
                return redirect()->back();
            }

            foreach ($rows as $row) {
                Coupon::updateOrCreate(
                    ['code' => $row['code']],
                    [
                        'code' => $row['code'],
                        'percent' => $row['percent'],
                    ]
                );
            }
        }
    }
}
