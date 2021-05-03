<?php
namespace hirohiro716\Scent;

/**
 * 数値をフォーマットするクラス。
 * 
 * @author hiro
 */
class NumberFormat extends AbstractObject
{
    
    private $decimals;
    
    /**
     * コンストラクタ。
     *
     * @param mixed $value
     */
    public function __construct($decimals = 0)
    {
        parent::__construct();
        $this->decimals = $decimals;
    }
    
    /**
     * 指定された値をフォーマットした結果のStringObjectインスタンスを取得する。
     * 
     * @return StringObject
     */
    public function format($value): StringObject
    {
        $valueObject = new StringObject($value);
        $floatValue = $valueObject->toFloat();
        if (Helper::isNull($floatValue)) {
            $floatValue = 0;
        }
        return new StringObject(number_format($floatValue, $this->decimals));
    }
    
}