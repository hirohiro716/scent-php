<?php
namespace hirohiro716\Scent;

use ReflectionClass;

/**
 * Enumの抽象クラス.
 * 
 * @author hiro
 */
abstract class AbstractEnum
{
    
    private $name;
    
    private $value;
    
    /**
     * コンストラクタ.
     * 
     * @param string $name 定数名
     * @param mixed $value 定数
     */
    private function __construct(string $name, $value)
    {
        $this->name = new StringObject($name);
        $this->value = $value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
    
    /**
     * 定数の値を取得する.
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * 定数名を取得する.
     * 
     * @return string
     */
    public function getName(): StringObject
    {
        return $this->name;
    }
    
    /**
     * 定数の値と引数が同じか判定する.
     * 
     * @param mixed $value
     * @return bool
     */
    public function equals($value): bool
    {
        return $this->value === $value;
    }
    
    private static $instances = null;
    
    /**
     * すべての定数名の配列を作成する.
     */
    private static function createAllObject(): void
    {
        if (self::$instances === null) {
            self::$instances = new Hash();
            $class = new ReflectionClass(static::class);
            foreach ($class->getConstants() as $name => $value) {
                self::$instances->put($value, new static($name, $value));
            }
        }
    }
    
    /**
     * 一意の定数オブジェクトを取得する.
     *
     * @param mixed $constantValue
     * @return self|null 定数オブジェクト
     */
    public static function get($constantValue): self
    {
        self::createAllObject();
        if (self::$instances->isExistKey($constantValue) == false) {
            return null;
        }
        return self::$instances->get($constantValue);
    }
    
    /**
     * すべての定数を取得する.
     *
     * @return array
     */
    public static function values(): array
    {
        self::createAllObject();
        return self::$instances->getValues();
    }
    
}