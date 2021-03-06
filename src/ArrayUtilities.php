<?php

namespace Mehrkanal;

/**
 * Utilities class with helper functions for array handling
 *
 * @author Henrik Thesing <thesing@mehrkanal.com>
 */
class ArrayUtilities
{
    /**
     * Tests if an array is associative or not
     *
     * @param array $array array to check
     *
     * @return bool
     */
    public static function isAssoc(array $array)
    {
        $keys = array_keys($array);
        $assoc = (array_keys($keys) !== $keys);

        return $assoc;
    }

    /**
     * Tests if a value is an array with an additional check for array-like objects
     *
     * @param mixed $value value to check
     *
     * @return bool
     */
    public static function isArray($value)
    {
        if (is_array($value)) {
            return true;
        } else {
            return (is_object($value) AND $value instanceof \Traversable);
        }
    }

    /**
     * Recursive array search
     *
     * @param mixed $needle
     * @param array $haystack
     * @param array $indexes
     *
     * @throws \InvalidArgumentException
     * @return bool
     */
    public static function search($needle, $haystack, &$indexes = [])
    {
        if (!static::isArray($haystack)) {
            throw new \InvalidArgumentException("Values haystack is not an array.");
        }

        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $indexes[] = $key;
                $status = static::search($needle, $value, $indexes);
                if ($status) {
                    return true;
                } else {
                    $indexes = [];
                }
            } else if ($value == $needle) {
                $indexes[] = $key;
                return true;
            }
        }
        return false;
    }

    /**
     * Replace array values using specified map
     *
     * @param array $values
     * @param array $map
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function replace($values, array $map)
    {
        if (!static::isArray($values)) {
            throw new \InvalidArgumentException("Values to be replaced is not an array.");
        }

        $res = [];
        foreach ($values as $key => $value) {
            if (isset($map[$value])) {
                $res[$key] = $map[$value];
            }
        }

        return $res;
    }

    /**
     * Clears multiple keys of array
     *
     * @param array $data
     * @param array $keys
     *
     * @return array
     */
    public static function clear($data, $keys)
    {
        if (empty($keys)) {
            return $data;
        }
        if (!static::isArray($keys)) {
            $keys = array($keys);
        }

        foreach ($keys as $key) {
            unset($data[$key]);
        }

        return $data;
    }

    /**
     * Skip first (or last) elements of an array (indexed from 0-n)
     *
     * @param $array
     * @param $n
     * @param $tail
     *
     * @return mixed
     */
    public static function skip($array, $n, $tail = false)
    {
        if (static::isAssoc($array)) {
            throw new \InvalidArgumentException("Given array needs to be an indexed array, found associative");
        }
	
        $c = count($array);

        if ($tail) {
            for ($i = $c - $n; $i < $c; $i++) {
                unset($array[$i]);
            }
        } else {
            for ($i = 0; $i < $n; $i++) {
                unset($array[$i]);
            }
        }

        return $array;
    }

    /**
     * Shuffle an array, preserve associative keys
     * Not in place like in shuffle()
     *
     * @param $array
     *
     * @return mixed
     */
    public static function shuffle($array)
    {
        $keys = array_keys($array);

        shuffle($keys);

        $res = array();
        foreach ($keys as $key) {
            $res[$key] = $array[$key];
        }

        return $res;
    }

    /**
     * Returns the max depth of the given array
     *
     * @param array $array
     *
     * @return int
     */
    public static function depth(array $array)
    {
        $depth = 1;

        foreach ($array as $value) {
            if (is_array($value)) {
                $current_depth = static::depth($value) + 1;
                if ($current_depth > $depth) {
                    $depth = $current_depth;
                }
            }
        }

        return $depth;
    }
}
