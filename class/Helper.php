<?php
namespace hirohiro716\Scent;

/**
 * PHPの基本的な関数。
 *
 * @author hiro
 */
class Helper
{

    /**
     * 変数がnullかどうかを判定する。
     *
     * @param mixed $value
     * @return bool
     */
    public static function isNull($value): bool
    {
        return is_null($value);
    }

    /**
     * オブジェクトまたはスカラーの名前を取得する。
     *
     * @param mixed $scalarObject
     * @return string boolean、integer、double、string、array、NULL、またはクラス名
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
     * オブジェクトまたはスカラーが指定した名前と等しいか判定する. クラス名の比較はnamespaceを含めることが可能。
     *
     * @param mixed $scalarObject スカラーまたはインスタンス
     * @param string $name bool、boolean、int、integer、double、float、string、array、null、NULL、またはクラス名
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
    
    /**
     * オブジェクトのIDを取得する。
     * 
     * @param mixed $object
     * @return string
     */
    public static function getInstanceId($object): string
    {
        return spl_object_hash($object);
    }
    
    /**
     * 通信が暗号化されている場合はtrueを返す。
     *
     * @return bool
     */
    public static function isHTTPS(): bool
    {
        if (ArrayHelper::isExistKey($_SERVER, "HTTPS")) {
            $https = new StringObject($_SERVER["HTTPS"]);
            return $https->equals("off") == false;
        }
        return false;
    }
    
    /**
     * 指定されたURLが存在する場合はtrueを返す。
     * 
     * @param string $url
     * @param int $timeout
     * @return bool
     */
    public static function isExistURL(string $url, int $timeoutSeconds): bool
    {
        $curlHandle = curl_init($url);
        curl_setopt($curlHandle, CURLOPT_NOBODY, true);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeoutSeconds);
        curl_exec($curlHandle);
        $statusCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);
        if ($statusCode == 200) {
            return true;
        }
        return false;
    }
}