<?php

namespace App\Helpers;

class Helper
{
    public static function createSlug($id, $string)
    {
        return $id.'-'.str_replace(['/', ' '], '-', $string);
    }

    /*
     * cut first $length symbol from string if string length > $length
     */
    public static function cutString($string, $length)
    {
        if (mb_strlen($string, 'UTF-8') > $length) {
            $result = mb_substr($string, 0, $length, 'UTF-8').'...';
        } else {
            $result = $string;
        }

        return $result;
    }
}
