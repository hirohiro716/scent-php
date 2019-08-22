<?php
namespace hirohiro716\Scent;

use Exception;

/**
 * セッションクラス.
 *
 * @author hiro
 */
class Session extends AbstractObject
{

    private const KEY_AGENT = "session_key_agent";

    /**
     * コンストラクタ.
     * 
     * @param mixed $lifetime セッションクッキーの有効期限(秒数)
     * @param bool $isSecure HTTPSのみ許可するかどうか
     */
    public function __construct($lifetime = null, bool $isSecure = false)
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            // サーバーで生成していないセッションIDは受け付けない
            ini_set("session.use_strict_mode", true);
            // javascriptからcookieのセッションIDにアクセスさせない
            ini_set("session.cookie_httponly", true);
            // 有効期限をセット
            $lifetimeObject = new StringObject($lifetime);
            if (Helper::isNull($lifetimeObject->toInteger()) == false) {
                ini_set("session.cookie_lifetime", $lifetimeObject->toInteger());
            }
            // HTTPSのみ許可するかどうか
            if ($isSecure) {
                ini_set("session.cookie_secure", true);
            }
            // セッション開始
            session_start();
        }
        // 別のブラウザからのアクセスなら初期化
        if ($_SESSION[self::KEY_AGENT]) {
            $agent = new StringObject($_SESSION[self::KEY_AGENT]);
            if ($agent->equals($_SERVER["HTTP_USER_AGENT"]) === false) {
                session_unset();
            }
        }
        $_SESSION[self::KEY_AGENT] = $_SERVER["HTTP_USER_AGENT"];
        // セッションIDを変更
        session_regenerate_id(true);
    }

    /**
     * セッションに値をセットする.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function put($key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * セッションの値を取得する.
     *
     * @param mixed $key
     * @return mixed
     */
    public function get($key)
    {
        if ($this->isExistKey($key)) {
            return $_SESSION[$key];
        }
        return null;
    }

    /**
     * セッションの値が存在するか確認する.
     *
     * @param mixed $key
     * @return bool
     */
    public function isExistKey($key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * セッションの値を破棄する.
     *
     * @param mixed $key
     */
    public function remove($key): void
    {
        if ($this->isExistKey($key)) {
            unset($_SESSION[$key]);
        }
    }
    
    private const KEY_TOKEN = "session_key_token";

    /**
     * クロスサイトリクエストフォージェリ(CSRF)対策のTokenを生成する.
     *
     * @return string
     */
    public function createToken(): string
    {
        $token = StringObject::createRandomString(32);
        $this->put(self::KEY_TOKEN, $token);
        return $token;
    }

    /**
     * ユーザーから送信されたTokenと発行したTokenと一致するか判定する.
     *
     * @param string $token
     *            ユーザーが送信してきたtoken
     * @return bool
     */
    public function isValidToken(string $token): bool
    {
        if ($this->isExistKey(self::KEY_TOKEN)) {
            $masterToken = new StringObject($this->get(self::KEY_TOKEN));
            if ($masterToken->equals($token) && $masterToken->length() > 0) {
                return true;
            }
        }
        return false;
    }
}