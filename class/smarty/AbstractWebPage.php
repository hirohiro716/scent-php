<?php
namespace hirohiro716\Scent\Smarty;

use Smarty;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\ArrayHelper;
use hirohiro716\Scent\Hash;
use hirohiro716\Scent\AbstractObject;

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
     * @return string
     */
    public function getPostValue(string $name): string
    {
        $post = new Hash($_POST);
        if ($post->isExistKey($name) == false) {
            return "";
        }
        $value = new StringObject($post->get($name));
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
        $get = new Hash($_GET);
        if ($get->isExistKey($name) == false) {
            return "";
        }
        $value = new StringObject($get->get($name));
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
        $request = new Hash($_REQUEST);
        if ($request->isExistKey($name) == false) {
            return "";
        }
        $value = new StringObject($request->get($name));
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
    
}