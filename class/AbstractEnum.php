<?php
namespace hirohiro716\Scent;

use ReflectionClass;

/**
 * Enumの抽象クラス.
 * 
 * @author hiro
 */
abstract class AbstractEnum extends AbstractObject
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
        parent::__construct();
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
    
    /**
     * 定義されていない値を示す定数を取得する.
     * 
     * @var AbstractEnum
     */
    public static $NULL = null;
    
    private static $instancesArray = null;
    
    /**
     * すべての定数名の配列を作成する.
     */
    private static function createAllObject(): void
    {
        if (self::$NULL === null) {
            self::$NULL = new NullEnum("NULL", null);
        }
        if (self::$instancesArray === null) {
            self::$instancesArray = new Hash();
        }
        $class = new ReflectionClass(static::class);
        $className = $class->getName();
        if (self::$instancesArray->isExistKey($className) == false) {
            $instances = new Hash();
            foreach ($class->getConstants() as $name => $value) {
                $instances->put($value, new static($name, $value));
            }
            self::$instancesArray->put($className, $instances);
        }
    }
    
    /**
     * 一意の定数オブジェクトを取得する.
     *
     * @param mixed $constantValue
     * @return self 定数オブジェクト
     */
    public static function const($constantValue): self
    {
        self::createAllObject();
        $class = new ReflectionClass(static::class);
        $className = $class->getName();
        $instances = self::$instancesArray->get($className);
        if ($instances->isExistKey($constantValue) == false) {
            return self::$NULL;
        }
        return $instances->get($constantValue);
    }
    
    /**
     * 定数が定義されているか判定する.
     * 
     * @param mixed $constantValue
     * @return bool
     */
    public static function isExistConstant($constantValue): bool
    {
        if (static::const($constantValue) === self::$NULL) {
            return false;
        }
        return true;
    }
    
    /**
     * すべての定数を取得する.
     *
     * @return array
     */
    public static function values(): array
    {
        self::createAllObject();
        $class = new ReflectionClass(static::class);
        $className = $class->getName();
        $instances = self::$instancesArray->get($className);
        return $instances->getValues();
    }
    
}

/**
 * 未定義の値を示す定数クラス.
 * 
 * @author hiro
 */
class NullEnum extends AbstractEnum {
    
    public function __toString(): string
    {
        return "";
    }
    
}
