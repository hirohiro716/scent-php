<?php
namespace hirohiro716\Scent;

/**
 * PHPの基本的な関数.
 *
 * @author hiro
 */
class Helper
{

    /**
     * 変数がnullかどうかを判定する.
     *
     * @param mixed $value
     * @return bool
     */
    public static function isNull($value): bool
    {
        return is_null($value);
    }
    
    /**
     * オブジェクトまたはスカラが指定した名前と等しいか判定する.<br>
     *
     * @param mixed $scalarObject
     *            スカラまたはインスタンス
     * @param string $name
     *            bool | boolean | int | integer | double | float | string | array | null | NULL またはクラス名
     * @return bool
     */
    public static function instanceIsThisName($scalarObject, string $name): bool
    {
        $typeName = gettype($scalarObject);
        $subName = "";
        switch ($typeName) {
            case "object":
                return get_class($scalarObject) == $name;
            case "boolean":
                $subName = "bool";
                break;
            case "integer":
                $subName = "int";
                break;
            case "double":
                $subName = "float";
                break;
            case "NULL":
                $subName = "null";
                break;
        }
        $nameObject = new StringObject($name);
        return $nameObject->equals($typeName) || $nameObject->equals($subName);
    }
}