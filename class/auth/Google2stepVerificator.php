<?php
namespace hirohiro716\Scent\Auth;

use hirohiro716\Scent\Filesystem\File;
use hirohiro716\Scent\Image\QRCodeCreator;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\Helper;
use hirohiro716\Scent\AbstractObject;

/**
 * Google2段階認証を処理するクラス.
 *
 * @author hiro
 */
class Google2stepVerificator extends AbstractObject
{
    
    /**
     * コンストラクタ.
     *
     * @param string $secretKey
     */
    public function __construct(string $secretKey = null)
    {
        $this->secretKey = $secretKey;
    }

    private $secretKey;
    
    /**
     * 内部のシークレットキーを取得する.
     * 
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }
    
    /**
     * シークレットキーを作成する.
     *
     * @return string
     */
    public function createNewSecretKey(): string
    {
        $authenticator = new \PHPGangsta_GoogleAuthenticator();
        $this->secretKey = $authenticator->createSecret();
        return $this->secretKey;
    }

    /**
     * 認証を行う.
     *
     * @param string $onetimeCode
     *            ワンタイムコード
     * @return bool
     */
    public function verify(string $onetimeCode): bool
    {
        if (Helper::isNull($this->secretKey)) {
            throw new \LogicException("Secret key is empty.");
        }
        $authenticator = new \PHPGangsta_GoogleAuthenticator();
        return $authenticator->verifyCode($this->secretKey, $onetimeCode, 1);
    }

    /**
     * Google認証アプリ用のQRコードを作成する.
     *
     * @param string $title
     *            タイトル
     * @param File $file
     *            保存場所
     * @param float $scale
     *            QRコードの大きさのスケール(初期値は1)
     */
    public function createQRCode(string $title, File $file, float $scale)
    {
        if (Helper::isNull($this->secretKey)) {
            throw new \LogicException("Secret key is empty.");
        }
        $url = new StringObject("otpauth://totp/");
        $titleObject = new StringObject($title);
        $url->append($titleObject->urlencode());
        $url->append("?secret=");
        $url->append($this->secretKey);
        $creator = new QRCodeCreator();
        $creator->setScale($scale);
        $creator->create($url, $file);
    }
}