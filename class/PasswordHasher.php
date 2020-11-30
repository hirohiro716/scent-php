<?php
namespace hirohiro716\Scent;

/**
 * ハッシュ化するクラス。
 * 
 * @author hiro
 */
class PasswordHasher extends AbstractObject
{
    
    private $value;
    
    private $hash;
    
    /**
     * コンストラクタ。
     * 
     * @param string $value
     */
    public function __construct(string $value)
    {
        parent::__construct();
        $this->value = $value;
        $this->hash = password_hash($value, PASSWORD_DEFAULT);
    }
    
    /**
     * ハッシュ値を取得する。
     * 
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }
    
    /**
     * 内部の値とハッシュ値が一致する場合はtrueを返す。
     * 
     * @param string $hash
     * @return bool
     */
    public function verify(string $hash): bool
    {
        return password_verify($this->value, $hash);
    }
    
}