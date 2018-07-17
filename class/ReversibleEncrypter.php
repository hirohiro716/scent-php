<?php
namespace hirohiro716\Scent;

/**
 * 可逆暗号化するクラス.
 * 
 * @author hiro
 */
class ReversibleEncrypter
{
    
    private $method = "AES-256-CBC";
    
    private $key;
    
    private $iv;
    
    /**
     * コンストラクタ.
     * 
     * @param string $key
     * @param string $iv
     */
    public function __construct(string $key, string $iv = "")
    {
        $this->key = $key;
        $this->iv = $iv;
    }
    
    /**
     * 初期化ベクトルを新規生成する.
     */
    public function createIV(): void
    {
        $length = openssl_cipher_iv_length($this->method);
        $this->iv = openssl_random_pseudo_bytes($length);
    }
    
    /**
     * 内部で生成したIVを取得する.
     * 
     * @return string
     */
    public function getIV(): string
    {
        return $this->iv;
    }
    
    /**
     * 可逆暗号化する.
     * 
     * @param string $target
     * @return string
     */
    public function encrypt(string $target): string
    {
        return openssl_encrypt($target, $this->method, $this->key, 0, $this->iv);
    }
    
    /**
     * 暗号化されている文字列を復号化する.
     * 
     * @param string $encrypted
     * @return string
     */
    public function decrypt(string $encrypted): string
    {
        return openssl_decrypt($encrypted, $this->method, $this->key, 0, $this->iv);
    }
    
}