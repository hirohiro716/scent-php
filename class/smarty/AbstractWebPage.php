<?php
namespace hirohiro716\Scent\Smarty;

use Smarty;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\ArrayHelper;
use hirohiro716\Scent\Hash;
use hirohiro716\Scent\AbstractObject;
use hirohiro716\Scent\Helper;

/**
 * Webページの抽象クラス.
 * 
 * @author hiro
 */
abstract class AbstractWebPage extends AbstractObject
{
    
    /**
     * コンストラクタ.
     */
    public function __construct()
    {
        parent::__construct();
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($this->getTemplateDirectory());
        $this->smarty->setCompileDir($this->getCompileDirectory());
        $this->smarty->setLeftDelimiter($this->getLeftDelimiter());
        $this->smarty->setRightDelimiter($this->getRightDelimiter());
    }
    
    private $smarty;
    
    /**
     * Smartyインスタンスを取得する.
     * 
     * @return Smarty
     */
    public function getSmarty(): Smarty
    {
        return $this->smarty;
    }
    
    /**
     * Smartyのテンプレートディレクトリを取得する.
     *
     * @return string
     */
    public abstract function getTemplateDirectory(): string;
    
    /**
     * Smartyのキャッシュディレクトリを取得する.
     *
     * @return string
     */
    public abstract function getCompileDirectory(): string;
    
    /**
     * Smartyの左デリミタを取得する.
     *
     * @return string
     */
    public abstract function getLeftDelimiter(): string;
    
    /**
     * Smartyの右デリミタを取得する.
     *
     * @return string
     */
    public abstract function getRightDelimiter(): string;
    
    /**
     * Smartyのテンプレートファイルの場所をテンプレートディレクトリからの相対パスで取得する.
     * 
     * @return string
     */
    public abstract function getTemplateFileLocation(): string;
    
    /**
     * テンプレートファイルを表示する.
     */
    public function display(): void
    {
        $this->smarty->display(static::getTemplateFileLocation());
    }
    
    /**
     * テンプレートファイルに値を割り当てる.
     * 
     * @param mixed $key キー
     * @param mixed $value 値
     */
    public function assign($key, $value): void
    {
        $this->smarty->assign($key, $value);
    }
    
    /**
     * ほかのページにリダイレクトする.
     * 
     * @param string $URL
     */
    public function redirect(string $URL): void
    {
        header("location: " . $URL);
        exit();
    }
    
    /**
     * 通信が暗号化されているか判定する.
     * 
     * @return bool
     */
    public function isHTTPS(): bool
    {
        if (ArrayHelper::isExistKey($_SERVER, "HTTPS")) {
            $https = new StringObject($_SERVER["HTTPS"]);
            return $https->equals("off") == false;
        }
        return false;
    }
    
    /**
     * $_POSTの指定値を取得する.
     * 
     * @param string $name
     * @return mixed
     */
    public function getPostValue(string $name)
    {
        $hash = new Hash($_POST);
        if ($hash->isExistKey($name) == false) {
            return "";
        }
        $value = $hash->get($name);
        switch (Helper::findInstanceName($value)) {
            case "array":
                return self::sanitizeArray($value);
            default:
                $valueObject = new StringObject($hash->get($name));
                return $valueObject->sanitize();
        }
    }
    
    /**
     * $_POSTの値をすべて取得する.
     * 
     * @return Hash
     */
    public function getPostValues(): Hash
    {
        $hash = new Hash();
        foreach (ArrayHelper::extractKeys($_POST) as $key) {
            $hash->put($key, self::getPostValue($key));
        }
        return $hash;
    }
    
    /**
     * $_GETの指定値を取得する.
     *
     * @param string $name
     * @return mixed
     */
    public function getGetValue(string $name)
    {
        $hash = new Hash($_GET);
        if ($hash->isExistKey($name) == false) {
            return "";
        }
        $value = $hash->get($name);
        switch (Helper::findInstanceName($value)) {
            case "array":
                return self::sanitizeArray($value);
            default:
                $valueObject = new StringObject($hash->get($name));
                return $valueObject->sanitize();
        }
    }
    
    /**
     * $_GETの値をすべて取得する.
     *
     * @return Hash
     */
    public function getGetValues(): Hash
    {
        $hash = new Hash();
        foreach (ArrayHelper::extractKeys($_GET) as $key) {
            $hash->put($key, self::getPostValue($key));
        }
        return $hash;
    }
    
    /**
     * $_REQUESTの指定値を取得する.
     *
     * @param string $name
     * @return mixed
     */
    public function getRequestValue(string $name)
    {
        $hash = new Hash($_REQUEST);
        if ($hash->isExistKey($name) == false) {
            return "";
        }
        $value = $hash->get($name);
        switch (Helper::findInstanceName($value)) {
            case "array":
                return self::sanitizeArray($value);
            default:
                $valueObject = new StringObject($hash->get($name));
                return $valueObject->sanitize();
        }
    }
    
    /**
     * $_REQUESTの値をすべて取得する.
     *
     * @return Hash
     */
    public function getRequestValues(): Hash
    {
        $hash = new Hash();
        foreach (ArrayHelper::extractKeys($_REQUEST) as $key) {
            $hash->put($key, self::getPostValue($key));
        }
        return $hash;
    }
    
    /**
     * 配列の中身をすべてサニタイジングする.
     * @param array $array 対象の配列
     * @return array サニタイズ後の配列
     */
    private function sanitizeArray(array $array): array
    {
        $newArray = array();
        foreach ($array as $key => $value) {
            switch (Helper::findInstanceName($value)) {
                case "array":
                    $newArray[$key] = self::sanitizeArray($value);
                    break;
                default:
                    $valueObject = new StringObject($value);
                    $newArray[$key] = $valueObject->sanitize()->get();
                    break;
            }
        }
        return $newArray;
    }
    
}