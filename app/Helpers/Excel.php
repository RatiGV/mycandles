<?php

namespace App\Helpers;

class Excel
{
    public static function download($orders)
    {
        $fileName = 'members-data_'.date('Y-m-d').'.xlsx';

        // Column names
        $fields = ['code', 'Address', 'first name', 'last name', 'total'];
        // Display column names as first row
        $excelData = implode("\t", array_values($fields))."\n";

        foreach ($orders->get() as $data) {
            $lineData = [$data['code'], $data['address'], $data['first_name'], $data['last_name'], $data['total']];
            $excelData .= implode("\t", array_values($lineData))."\n";
        }
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$fileName\"");

        // Render excel data
        return $excelData;
    }
}
