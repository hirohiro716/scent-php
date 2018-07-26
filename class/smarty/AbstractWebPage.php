<?php
namespace hirohiro716\Scent\Smarty;

use Smarty;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\ArrayHelper;
use hirohiro716\Scent\Hash;

/**
 * Webページの抽象クラス.
 * 
 * @author hiro
 */
abstract class AbstractWebPage
{
    
    /**
     * コンストラクタ.
     */
    public function __construct()
    {
        $this->smarty = new Smarty();
        if (self::$templateDirectory !== null) {
            $this->smarty->setTemplateDir(self::$templateDirectory);
        }
        if (self::$compileDirectory !== null) {
            $this->smarty->setCompileDir(self::$compileDirectory);
        }
        if (self::$leftDelimiter !== null) {
            $this->smarty->setLeftDelimiter(self::$leftDelimiter);
        }
        if (self::$rightDelimiter !== null) {
            $this->smarty->setRightDelimiter(self::$rightDelimiter);
        }
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
     * テンプレートファイルの場所を取得する.
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
     * @return string
     */
    public function getPostValue(string $name): string
    {
        $value = new StringObject($_POST[$name]);
        return $value->sanitize();
    }
    
    /**
     * $_POSTの値をすべて取得する.
     * 
     * @return Hash
     */
    public function getPostValues(): Hash
    {
        $hash = new Hash();
        foreach ($_POST as $key => $value) {
            $valueObject = new StringObject($value);
            $hash->put($key, $valueObject->sanitize()->get());
        }
        return $hash;
    }
    
    /**
     * $_GETの指定値を取得する.
     *
     * @param string $name
     * @return string
     */
    public function getGetValue(string $name): string
    {
        $value = new StringObject($_GET[$name]);
        return $value->sanitize();
    }
    
    /**
     * $_GETの値をすべて取得する.
     *
     * @return Hash
     */
    public function getGetValues(): Hash
    {
        $hash = new Hash();
        foreach ($_GET as $key => $value) {
            $valueObject = new StringObject($value);
            $hash->put($key, $valueObject->sanitize()->get());
        }
        return $hash;
    }
    
    /**
     * $_REQUESTの指定値を取得する.
     *
     * @param string $name
     * @return string
     */
    public function getRequestValue(string $name): string
    {
        $value = new StringObject($_REQUEST[$name]);
        return $value->sanitize();
    }
    
    /**
     * $_REQUESTの値をすべて取得する.
     *
     * @return Hash
     */
    public function getRequestValues(): Hash
    {
        $hash = new Hash();
        foreach ($_REQUEST as $key => $value) {
            $valueObject = new StringObject($value);
            $hash->put($key, $valueObject->sanitize()->get());
        }
        return $hash;
    }
    
    private static $templateDirectory = null;
    
    /**
     * Smartyで使用するテンプレートディレクトリをセットする.
     * 
     * @param string $directory
     */
    public static function setTempleteDirectory(string $directory): void
    {
        self::$templateDirectory = $directory;
    }
    
    private static $compileDirectory = null;
    
    /**
     * Smartyで使用するコンパイルディレクトリをセットする.
     *
     * @param string $directory
     */
    public static function setCompileDirectory(string $directory): void
    {
        self::$compileDirectory = $directory;
    }
    
    private static $leftDelimiter = null;
    
    /**
     * Smartyで使用するデリミタをセットする.
     *
     * @param string $delimiter
     */
    public static function setLeftDelimiter(string $delimiter): void
    {
        self::$leftDelimiter = $delimiter;
    }
    
    private static $rightDelimiter = null;
    
    /**
     * Smartyで使用するデリミタをセットする.
     *
     * @param string $delimiter
     */
    public static function setRightDelimiter(string $delimiter): void
    {
        self::$rightDelimiter = $delimiter;
    }
    
}