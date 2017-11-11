<?php

namespace Crust\Helpers;

class Utils
{
    public static function join(array $arr1, array $arr2)
    {
        if (count($arr2) == 0) return;
        if (self::isAssoc($arr1)) {
            return self::joinAssoc($arr1, $arr2);
        }
        else {
            return self::joinSeq($arr1, $arr2);
        }
    }

    public static function joinSeq($arr1, $arr2)
    {
        $dif = array_diff($arr2, $arr1);
        $arr1 = array_merge($arr1, $dif);
        return $arr1;
    }

    public static function joinAssoc($arr1, $arr2)
    {
        foreach ($arr1 as $key => $value) {
            if (is_string($key)) {
                if (array_key_exists($key, $arr2)) {
                    if (is_array($value)) {
                        if (is_array($arr2[$key])) {
                            $arr1[$key] = self::join($value, $arr2[$key]);
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
        }
        foreach ($arr2 as $key => $value) {
            if (is_string($key)) {
                if (!array_key_exists($key, $arr1)) {
                    $arr1[$key] = $value;
                }
            }
            elseif (is_int($key)) {
                if (!in_array($value, $arr1)) {
                    $arr1[] = $value;
                }
            }
        }
        return $arr1;
    }

    public static function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
