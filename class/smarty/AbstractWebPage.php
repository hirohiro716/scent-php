<?php
namespace hirohiro716\Scent\Smarty;

use Smarty;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\ArrayHelper;
use hirohiro716\Scent\Hash;
use hirohiro716\Scent\AbstractObject;
use hirohiro716\Scent\Helper;

/**
 * Webページの抽象クラス。
 * 
 * @author hiro
 */
abstract class AbstractWebPage extends AbstractObject
{
    
    /**
     * コンストラクタ。
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
     * Smartyインスタンスを取得する。
     * 
     * @return Smarty
     */
    public function getSmarty(): Smarty
    {
        return $this->smarty;
    }
    
    /**
     * Smartyのテンプレートディレクトリを取得する。
     *
     * @return string
     */
    public abstract function getTemplateDirectory(): string;
    
    /**
     * Smartyのキャッシュディレクトリを取得する。
     *
     * @return string
     */
    public abstract function getCompileDirectory(): string;
    
    /**
     * Smartyの左デリミタを取得する。
     *
     * @return string
     */
    public abstract function getLeftDelimiter(): string;
    
    /**
     * Smartyの右デリミタを取得する。
     *
     * @return string
     */
    public abstract function getRightDelimiter(): string;
    
    /**
     * Smartyのテンプレートファイルの場所をテンプレートディレクトリからの相対パスで取得する。
     * 
     * @return string
     */
    public abstract function getTemplateFileLocation(): string;
    
    /**
     * テンプレートファイルを表示する。
     */
    public function display(): void
    {
        $this->smarty->display(static::getTemplateFileLocation());
    }
    
    /**
     * テンプレートファイルに値を割り当てる。
     * 
     * @param mixed $key キー
     * @param mixed $value 値(stringとarrayは自動sanitizeする)
     */
    public function assign($key, $value): void
    {
        $this->smarty->assign($key, self::sanitize($value));
    }
    
    /**
     * 値をサニタイジングする。
     * 
     * @param mixed $value 対象の値
     * @return mixed サニタイズ後の値
     */
    private function sanitize($value)
    {
        switch (Helper::findInstanceName($value)) {
            case "array":
                $array = array();
                foreach ($value as $key => $innerValue) {
                    $array[$key] = self::sanitize($innerValue);
                }
                return $array;
            case "string":
                $valueObject = new StringObject($value);
                return $valueObject->sanitize()->get();
            default:
                return $value;
        }
    }
    
    /**
     * ほかのページにリダイレクトする。
     * 
     * @param string $URL
     */
    public function redirect(string $URL): void
    {
        self::redirectStatic($URL);
    }
    
    /**
     * ほかのページにリダイレクトする。
     *
     * @param string $URL
     */
    public static function redirectStatic(string $URL): void
    {
        header("location: " . $URL);
        exit();
    }
    
    /**
     * 通信が暗号化されている場合はtrueを返す。
     * 
     * @return bool
     */
    public function isHTTPS(): bool
    {
        return Helper::isHTTPS();
    }
    
    /**
     * $_POSTの指定値を取得する。
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
        return $hash->get($name);
    }
    
    /**
     * $_POSTの値をすべて取得する。
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
     * $_GETの指定値を取得する。
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
        return $hash->get($name);
    }
    
    /**
     * $_GETの値をすべて取得する。
     *
     * @return Hash
     */
    public function getGetValues(): Hash
    {
        $hash = new Hash();
        foreach (ArrayHelper::extractKeys($_GET) as $key) {
            $hash->put($key, self::getGetValue($key));
        }
        return $hash;
    }
    
    /**
     * $_REQUESTの指定値を取得する。
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
        return $hash->get($name);
    }
    
    /**
     * $_REQUESTの値をすべて取得する。
     *
     * @return Hash
     */
    public function getRequestValues(): Hash
    {
        $hash = new Hash();
        foreach (ArrayHelper::extractKeys($_REQUEST) as $key) {
            $hash->put($key, self::getRequestValue($key));
        }
        return $hash;
    }
    
}