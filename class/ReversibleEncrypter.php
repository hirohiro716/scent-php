<?php
namespace hirohiro716\Scent;

/**
 * 可逆暗号化するクラス。
 * 
 * @author hiro
 */
class ReversibleEncrypter extends AbstractObject
{
    
    private $method = "AES-256-CBC";
    
    private $key;
    
    private $iv;
    
    /**
     * コンストラクタ。
     * 
     * @param string $key
     * @param string $iv
     */
    public function __construct(string $key, string $iv = "")
    {
        parent::__construct();
        $this->key = $key;
        $this->iv = $iv;
    }
    
    /**
     * 初期化ベクトルを取得する。
     * 
     * @return string
     */
    public function getIV(): string
    {
        $ivObject = new StringObject($this->iv);
        if ($ivObject->length() == 0) {
            $length = openssl_cipher_iv_length($this->method);
            $stringObject = new StringObject(base64_encode(openssl_random_pseudo_bytes($length)));
            $this->iv = $stringObject->subString(0, $length);
        }
        return $this->iv;
    }
    
    /**
     * 可逆暗号化する。
     * 
     * @param string $target
     * @return string
     */
    public function encrypt(string $target): string
    {
        
        return openssl_encrypt($target, $this->method, $this->key, 0, $this->iv);
    }
    
    /**
     * 暗号化されている文字列を復号化する。
     * 
     * @param string $encrypted
     * @return string
     */
    public function decrypt(string $encrypted): string
    {
        return openssl_decrypt($encrypted, $this->method, $this->key, 0, $this->iv);
    }
    
    /**
     * 初期化ベクトルを新規生成する。
     * 
     * @return string 生成したIV
     */
    public static function createIV(): string
    {
        $instance = new self("");
        return $instance->getIV();
    }
    
}