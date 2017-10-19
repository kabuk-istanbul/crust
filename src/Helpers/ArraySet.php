<?php

namespace Crust\Helpers;

class ArraySet
{
    public static function join($arr1, $arr2)
    {
        if (self::isAssoc($arr1)) {
            return self::joinAssoc($arr1, $arr2);
        }
        else {
            return self::joinSeq($arr1, $arr2);
        }
    }

    public static function joinSeq($arr1, $arr2)
    {
        $dif = array_diff($arr1, $arr2);
        $arr1 = array_merge($arr1, $dif);
        return $arr1;
    }

    public static function joinAssoc($arr1, $arr2)
    {
        foreach ($arr1 as $key => $value) {
            if (array_key_exists($key, $arr2)) {
                if (is_array($value)) {
                    if (is_array($arr2[$key])) {
                        $arr1[$key] = self::joinArrays($value, $arr2[$key]);
                    }
                    else {
                        $arr1[$key] = $arr2[$key];
                    }
                }
                else {
                    $arr1[$key] = $arr2[$key];
                }
            }
        }
        $dif = array_diff_key($arr1, $arr2);
        $arr1 = array_merge($arr1, $dif);
        return $arr1;
    }

    public static function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
