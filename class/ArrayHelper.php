<?php
namespace hirohiro716\Scent;

/**
 * 配列に関する関数。
 *
 * @author hiro
 */
class ArrayHelper
{
    
    /**
     * 変数が配列の場合はtrueを返す。
     *
     * @param mixed $maybeIsArray
     * @return bool
     */
    public static function isArray($maybeIsArray): bool
    {
        return is_array($maybeIsArray);
    }
    
    /**
     * 配列内に特定のキーを持つ値がある場合はtrueを返す。
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
     * 厳密な型比較を用いて配列内の値と指定値を比較し、同じものがある場合はtrueを返す。
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
     * 配列内の特定のキーに対する値を取り除く。
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
     * 配列内の特定の値をすべて取り除く。
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
    
    /**
     * 配列の要素数を数える。
     * 
     * @param array $array
     * @return int
     */
    public static function count(array &$array): int
    {
        return count($array);
    }
    
    /**
     * 配列を結合して新しい配列を生成する。
     * 
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function merge(array $array1, array $array2): array
    {
        return array_merge($array1, $array2);
    }
    
    /**
     * 配列のキーをすべて取り出して配列で取得する。
     * 
     * @param array $array
     * @return array
     */
    public static function extractKeys(array $array): array
    {
        return array_keys($array);
    }
}
