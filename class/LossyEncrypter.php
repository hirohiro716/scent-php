<?php
namespace hirohiro716\Scent;

/**
 * 不可逆暗号化するクラス.
 * 
 * @author hiro
 */
class LossyEncrypter
{
    
    private $value;
    
    private $hash;
    
    /**
     * コンストラクタ.
     * 
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
        $this->hash = password_hash($value, PASSWORD_DEFAULT);
    }
    
    /**
     * 暗号化されたハッシュ値を取得する.
     * 
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }
    
    /**
     * 内部の値とハッシュ値が一致するか検証する.
     * 
     * @param string $hash
     * @return bool
     */
    public function verify(string $hash): bool
    {
        return password_verify($this->value, $hash);
    }
    
}