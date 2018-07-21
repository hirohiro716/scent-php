<?php

/**
 * 配列に関する関数.
 *
 * @author hiro
 */
class ArrayHelper
{
    
    /**
     * 変数が配列かどうかを判定する.
     *
     * @param mixed $maybeIsArray
     * @return bool
     */
    public static function isArray($maybeIsArray): bool
    {
        return is_array($maybeIsArray);
    }
    
    /**
     * 配列内に特定のキーを持つ値があるかどうか判定する.
     * 
     * @param array $array
     * @param mixed $key
     * @return bool
     */
    public static function isExistKey(array &$array, $key): bool
    {
        return array_key_exists($key, $array);
    }
    
    /**
     * 配列内に特定の値があるかどうか厳密な型比較を用いて判定する.
     * 
     * @param array $array
     * @param mixed $value
     * @return bool
     */
    public static function isExistValue(array &$array, $value): bool
    {
        if (array_search($value, $array, true) === false) {
            return false;
        }
        return true;
    }
    
    /**
     * 配列内の特定のキーに対する値を取り除く.
     *
     * @param array $array
     * @param mixed $key
     */
    public static function removeKey(array &$array, $key): void
    {
        if (self::isExistKey($array, $key)) {
            unset($array[$key]);
        }
    }
    
    /**
     * 配列内の特定の値をすべて取り除く.
     * 
     * @param array $array
     * @param mixed $value
     */
    public static function removeValue(array &$array, $value): void
    {
        $key = array_search($value, $array, true);
        while ($key !== false) {
            self::removeKey($array, $key);
            $key = array_search($value, $array, true);
        }
    }
    
}
