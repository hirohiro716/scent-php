<?php
namespace hirohiro716\Scent;

/**
 * Cookieのクラス。
 *
 * @author hiro
 */
class Cookie extends AbstractObject
{
    
    /**
     * コンストラクタ。
     *
     * @param mixed $lifetimeSecond セッションクッキーの有効期限(秒数)
     * @param bool $isSecure HTTPSのみ許可するかどうか
     */
    public function __construct($path = "/", $lifetimeSecond = null, bool $isSecure = false)
    {
        parent::__construct();
        $this->path = $path;
        $this->lifetimeSecond = $lifetimeSecond;
        $this->isSecure = $isSecure;
    }
    
    private $path;
    
    private $lifetimeSecond;
    
    private $isSecure;
    
    /**
     * Cookieに値をセットする。
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function put($key, $value): void
    {
        $datetime = new Datetime();
        if (Helper::isNull($this->lifetimeSecond) == false) {
            $datetime->addSecond($this->lifetimeSecond);
        }
        setcookie($key, $value, $datetime->toTimestamp(), $this->path, null, $this->isSecure, true);
    }
    
    /**
     * Cookieの値を取得する。
     *
     * @param mixed $key
     * @return mixed
     */
    public function get($key)
    {
        if ($this->isExistKey($key)) {
            return $_COOKIE[$key];
        }
        return null;
    }
    
    /**
     * Cookieの値が存在するか確認する。
     *
     * @param mixed $key
     * @return bool
     */
    public function isExistKey($key): bool
    {
        return isset($_COOKIE[$key]);
    }
    
    /**
     * Cookieの値を破棄する。
     *
     * @param mixed $key
     */
    public function remove($key): void
    {
        if ($this->isExistKey($key)) {
            $datetime = new Datetime();
            $datetime->addSecond(-1800);
            setcookie($key, null, $datetime->toTimestamp(), $this->path, null, $this->isSecure, true);
        }
    }
}