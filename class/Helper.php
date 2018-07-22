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
     * オブジェクトまたはスカラの名前を取得する.
     *
     * @param mixed $scalarObject
     * @return string クラス名 または boolean | integer | double | string | array | NULL
     */
    public static function findInstanceName($scalarObject): StringObject
    {
        $typeName = new StringObject(gettype($scalarObject));
        switch (true) {
            case $typeName->equals("object"):
                return new StringObject(get_class($scalarObject));
            default:
                return $typeName;
        }
    }

    /**
     * オブジェクトまたはスカラが指定した名前と等しいか判定する. クラス名の比較はnamespaceを含めることが可能.
     *
     * @param mixed $scalarObject
     *            スカラまたはインスタンス
     * @param string $name
     *            bool | boolean | int | integer | double | float | string | array | null | NULL またはクラス名
     * @return bool
     */
    public static function instanceIsThisName($scalarObject, string $name): bool
    {
        $instanceName = self::findInstanceName($scalarObject);
        $subName = "";
        switch (true) {
            case $instanceName->equals("boolean"):
                $subName = "bool";
                break;
            case $instanceName->equals("integer"):
                $subName = "int";
                break;
            case $instanceName->equals("double"):
                $subName = "float";
                break;
            case $instanceName->equals("NULL"):
                $subName = "null";
                break;
        }
        $nameObject = new StringObject($name);
        return $instanceName->length() - $instanceName->lastIndexOf($name) == $nameObject->length() || $nameObject->equals($subName);
    }

}