<?php
namespace hirohiro716\Scent;

use Iterator;
use ReflectionClass;

/**
 * Enumの抽象クラス。
 * 
 * @author hiro
 */
abstract class AbstractEnum extends AbstractObject
{
    
    private $name;
    
    private $value;
    
    /**
     * コンストラクタ。
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
     * 定数の値を取得する。
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * 定数名を取得する。
     * 
     * @return string
     */
    public function getName(): StringObject
    {
        return $this->name;
    }
    
    /**
     * 定数の値と引数が同じ場合はtrueを返す。
     * 
     * @param mixed $value
     * @return bool
     */
    public function equals($value): bool
    {
        return $this->value === $value;
    }
    
    private static $nullValue = null;
    
    /**
     * 定義されていない値を示す定数を取得する。
     * 
     * @return NullEnum
     */
    public static function NULL(): NullEnum
    {
        return self::$nullValue;
    }
    
    private static $instancesArray = null;
    
    /**
     * すべての定数名の配列を作成する。
     */
    private static function createAllObject(): void
    {
        if (self::$nullValue === null) {
            self::$nullValue = new NullEnum("NULL", null);
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
     * 一意の定数オブジェクトを取得する。
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
            return self::$nullValue;
        }
        return $instances->get($constantValue);
    }
    
    /**
     * 一意の定数オブジェクトを検索する。
     * 
     * @param string $name 大文字小文字を区別しないconstの定義名
     * @return self 定数オブジェクト
     */
    public static function find(string $constName): self
    {
        self::createAllObject();
        $class = new ReflectionClass(static::class);
        $className = $class->getName();
        $instances = self::$instancesArray->get($className);
        foreach ($instances as $instance) {
            if ($instance->getName()->equals($constName) || $instance->getName()->toLower()->equals($constName)) {
                return $instance;
            }
        }
        return self::$nullValue;
    }
    
    /**
     * 定数が定義されている場合はtrueを返す。
     * 
     * @param mixed $constantValue
     * @return bool
     */
    public static function isExistConstant($constantValue): bool
    {
        if (static::const($constantValue) === self::$nullValue) {
            return false;
        }
        return true;
    }
    
    /**
     * すべての定数を取得する。
     *
     * @return EnumValues
     */
    public static function values(): EnumValues
    {
        self::createAllObject();
        $class = new ReflectionClass(static::class);
        $className = $class->getName();
        $instances = self::$instancesArray->get($className);
        return new EnumValues($instances->getValues());
    }
    
}

/**
 * 未定義の値を示す定数クラス。
 *
 * @author hiro
 */
class NullEnum extends AbstractEnum {
    
    public function __toString(): string
    {
        return "";
    }
    
}

/**
 * EnumのIterator実装クラス。
 * 
 * @author hiro
 */
class EnumValues implements Iterator
{
    
    /**
     * コンストラクタ。
     * 
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }
    
    private $values = array();
    
    /**
     * すべての定数を配列で取得する。
     * 
     * @return array
     */
    public function toArray(): array
    {
        return $this->values;
    }
    
    private $position = 0;
    
    /**
     * 現在の要素を返す。
     * 
     * @return AbstractEnum
     */
    public function current(): AbstractEnum
    {
        return $this->values[$this->position];
    }
    
    /**
     * 現在の要素のキーを返す。
     *
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }
    
    /**
     * 次の要素に進む。
     */
    public function next(): void
    {
        $this->position++;
    }
    
    /**
     * イテレータの最初の要素に巻き戻す。
     */
    public function rewind(): void
    {
        $this->position = 0;
    }
    
    /**
     * 現在位置が有効な場合はtrueを返す。
     *
     * @return bool
     */
    public function valid(): bool
    {
        return ArrayHelper::isExistKey($this->values, $this->position);
    }
    
}
