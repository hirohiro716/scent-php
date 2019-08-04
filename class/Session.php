<?php
namespace hirohiro716\Scent;

/**
 * セッションクラス.
 *
 * @author hiro
 */
class Session extends AbstractObject
{

    private const KEY_AGENT = "session_key_agent";

    private const KEY_SID_LIMIT = "session_key_sid_limit";

    /**
     * コンストラクタ.
     */
    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            // サーバーで生成していないセッションIDは受け付けない
            ini_set("session.use_strict_mode", true);
            // javascriptからcookieのセッションIDにアクセスさせない
            ini_set("session.cookie_httponly", true);
            session_start();
            // 別のブラウザからのアクセスなら初期化
            if ($_SESSION) {
                $agent = new StringObject($_SESSION[self::KEY_AGENT]);
                if ($agent->equals($_SERVER["HTTP_USER_AGENT"]) === false) {
                    $hash = new Hash($_SESSION);
                    foreach ($hash->getKeys() as $key) {
                        unset($_SESSION[$key]);
                    }
                }
            }
            $_SESSION[self::KEY_AGENT] = $_SERVER["HTTP_USER_AGENT"];
            // 有効期限を過ぎているならID変更
            $isRegenerate = true;
            if ($_SESSION[self::KEY_SID_LIMIT]) {
                if ($_SESSION[self::KEY_SID_LIMIT] > Datetime::currentTime()) {
                    $isRegenerate = false;
                }
            }
            if ($isRegenerate) {
                $datetime = new Datetime();
                $datetime->addSecond(5);
                $_SESSION[self::KEY_SID_LIMIT] = $datetime->toTimestamp();
                session_regenerate_id(true);
            }
        }
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