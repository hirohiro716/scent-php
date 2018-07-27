<?php
namespace hirohiro716\Scent;

/**
 * 浮動小数点数のクラス.
 * 
 * @author hiro
 */
class FloatObject extends AbstractObject
{
    
    private $value = 0;
    
    /**
     * コンストラクタ.
     *
     * @param mixed $value
     */
    public function __construct($value = 0)
    {
        $this->value = (float) $value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
    
    /**
     * 値を取得する.
     *
     * @return float
     */
    public function get(): float
    {
        return (float) $this->value;
    }
    
    /**
     * 値をintとして取得する.
     *
     * @return int
     */
    public function toInteger(): int
    {
        return (int) $this->value;
    }
    
    /**
     * 小数以下を四捨五入されたFloatObjectインスタンスを取得する.
     * 
     * @param int $precision
     * @return FloatObject
     */
    public function round(): FloatObject
    {
        return new self(round($this->value, 0, PHP_ROUND_HALF_UP));
    }
    
    /**
     * 小数以下を切り上げたFloatObjectインスタンスを取得する.
     * 
     * @return FloatObject
     */
    public function ceil(): FloatObject
    {
        return new self(ceil($this->value));
    }
    
    /**
     * 小数以下を切り捨てたFloatObjectインスタンスを取得する.
     * 
     * @return FloatObject
     */
    public function floor(): FloatObject
    {
        return new self(floor($this->value));
    }
    
}