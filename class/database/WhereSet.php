<?php
namespace hirohiro716\Scent\Database;

use hirohiro716\Scent\ArrayHelper;
use hirohiro716\Scent\Hash;
use hirohiro716\Scent\StringObject;

/**
 * 複数のカラムをANDで連結するWHERE条件クラス.
 * 
 * @author hiro
 */
class WhereSet
{
    
    private $wheres = array();
    
    /**
     * 追加済みのWhereオブジェクトを取得する.
     * 
     * @return array
     */
    public function getWheres(): array
    {
        return $this->wheres;
    }
    
    /**
     * WHEREを追加する.
     * 
     * @param string $column カラム名
     * @param string $comparison 比較演算子
     * @param mixed $value 比較値
     * @param bool $isNot NOT論理演算子を使用するかどうか
     */
    public function add(string $column, string $comparison, $value, bool $isNot): void
    {
        $where = new Where($column, $comparison, $value);
        $where->setNot($isNot);
        $this->wheres[] = $where;
    }
    
    /**
     * 「=」を使用したWHEREを追加する.
     * 
     * @param string $column カラム名
     * @param mixed $value 比較値
     * @param bool $isNot NOT論理演算子を使用するかどうか
     */
    public function addEqual(string $column, $value, bool $isNot = false): void
    {
        $this->add($column, "=", $value, $isNot);
    }
    
    /**
     * 「!=」を使用したWHEREを追加する.
     * 
     * @param string $column カラム名
     * @param mixed $value 比較値
     * @param bool $isNot NOT論理演算子を使用するかどうか
     */
    public function addNotEqual(string $column, $value, bool $isNot = false): void
    {
        $this->add($column, "!=", $value, $isNot);
    }
    
    /**
     * 「<」を使用したWHEREを追加する.
     *
     * @param string $column カラム名
     * @param mixed $value 比較値
     * @param bool $isNot NOT論理演算子を使用するかどうか
     */
    public function addLess(string $column, $value, bool $isNot = false): void
    {
        $this->add($column, "<", $value, $isNot);
    }
    
    /**
     * 「<=」を使用したWHEREを追加する.
     *
     * @param string $column カラム名
     * @param mixed $value 比較値
     * @param bool $isNot NOT論理演算子を使用するかどうか
     */
    public function addLessEqual(string $column, $value, bool $isNot = false): void
    {
        $this->add($column, "<=", $value, $isNot);
    }
    
    /**
     * 「>」を使用したWHEREを追加する.
     *
     * @param string $column カラム名
     * @param mixed $value 比較値
     * @param bool $isNot NOT論理演算子を使用するかどうか
     */
    public function addGreater(string $column, $value, bool $isNot = false): void
    {
        $this->add($column, ">", $value, $isNot);
    }
    
    /**
     * 「>=」を使用したWHEREを追加する.
     *
     * @param string $column カラム名
     * @param mixed $value 比較値
     * @param bool $isNot NOT論理演算子を使用するかどうか
     */
    public function addGreaterEqual(string $column, $value, bool $isNot = false): void
    {
        $this->add($column, ">=", $value, $isNot);
    }
    
    /**
     * 「IN」を使用したWHEREを追加する.
     * 
     * @param string $column カラム名
     * @param array $values 比較値配列
     * @param bool $isNot NOT論理演算子を使用するかどうか
     */
    public function addIn(string $column, array $values, bool $isNot = false): void
    {
        $this->add($column, "IN", $values, $isNot);
    }
    
    /**
     * 「IS NULL」を使用したWHEREを追加する.
     * 
     * @param string $column カラム名
     * @param bool $isNot NOT論理演算子を使用するかどうか
     */
    public function addIsNull(string $column, bool $isNot = false): void
    {
        $this->add($column, "IS NULL", null, $isNot);
    }
    
    /**
     * 「LIKE」を使用したWHEREを追加する.
     *
     * @param string $column カラム名
     * @param mixed $value 比較値
     * @param bool $isNot NOT論理演算子を使用するかどうか
     */
    public function addLike(string $column, $value, bool $isNot = false): void
    {
        $this->add($column, "LIKE", $value, $isNot);
    }
    
    /**
     * 「BETWEEN」を使用したWHEREを追加する.
     *
     * @param string $column カラム名
     * @param mixed $valueFrom 比較値FROM
     * @param mixed $valueTo 比較値TO
     * @param bool $isNot NOT論理演算子を使用するかどうか
     */
    public function addBetween(string $column, $valueFrom, $valueTo, bool $isNot = false): void
    {
        $this->add($column, "BETWEEN", array($valueFrom, $valueTo), $isNot);
    }
    
    /**
     * 「SIMILAR TO」を使用したWHEREを追加する.
     *
     * @param string $column カラム名
     * @param mixed $value 比較値
     * @param bool $isNot NOT論理演算子を使用するかどうか
     */
    public function addSimilarTo(string $column, $value, bool $isNot = false): void
    {
        $this->add($column, "SIMILAR TO", $value, $isNot);
    }
    
    /**
     * 「REGEXP」を使用したWHEREを追加する.
     *
     * @param string $column カラム名
     * @param mixed $value 比較値
     * @param bool $isNot NOT論理演算子を使用するかどうか
     */
    public function addRegexp(string $column, $value, bool $isNot = false): void
    {
        $this->add($column, "REGEXP", $value, $isNot);
    }
    
    /**
     * 「カラム1 = ? AND カラム2 = ?」のようなパラメータWhere句を生成する.
     * 
     * @return string
     */
    public function buildParameterClause(): string
    {
        $whereString = new StringObject();
        foreach ($this->wheres as $where) {
            if ($whereString->length() > 0) {
                $whereString->append(" AND ");
            }
            $whereString->append($where->buildParameterClause());
        }
        return $whereString;
    }
    
    /**
     * buildParameterClauseメソッドで作成したWhere句に対するパラメータを生成する.
     * 
     * @return array すべてのパラメーターの配列
     */
    public function buildParameters(): array
    {
        $parameters = array();
        foreach ($this->wheres as $where) {
            foreach ($where->getValues() as $value) {
                $parameters[] = $value;
            }
        }
        return $parameters;
    }
    
}

/**
 * １つのカラムに対するWHERE条件クラス.
 * 
 * @author hiro
 */
class Where
{
    
    /**
     * コンストラクタ.
     * 
     * @param string $column カラム名
     * @param string $comparison 比較演算子
     * @param mixed $values 比較値
     */
    public function __construct(string $column, string $comparison, $values = null)
    {
        $this->column = $column;
        $comparisonObject = new StringObject($comparison);
        $this->comparison = $comparisonObject->toUpper()->trim()->get();
        if ($values !== null) {
            if (ArrayHelper::isArray($values)) {
                $this->values = $values;
            } else {
                $this->values = array($values);
            }
        }
    }
    
    private $column;
    
    /**
     * カラム名を取得する.
     * 
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }
    
    private $comparison;
    
    /**
     * 比較演算子を取得する.
     * 
     * @return string
     */
    public function getComparison(): string
    {
        return $this->comparison;
    }
    
    private $values;
    
    /**
     * 比較値を取得する.
     * 
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }
    
    /**
     * 1番目の比較値を取得する.
     * 
     * @return mixed
     */
    public function getValue1()
    {
        return $this->values[0];
    }
    
    /**
     * 2番目の比較値を取得する.
     * 
     * @return mixed
     */
    public function getValue2()
    {
        return $this->values[1];
    }
    
    private $isNot = false;
    
    /**
     * WHERE句がNOT論理演算子を使用するかどうか.
     * 
     * @return bool
     */
    public function isNot(): bool
    {
        return $this->isNot;
    }
    
    /**
     * WHERE句にNOT論理演算子を使用するかどうかをセットする.
     * 
     * @param bool $isNot
     */
    public function setNot(bool $isNot): void
    {
        $this->isNot = $isNot;
    }
    
    /**
     * 「カラム = ?」のようなパラメータWHERE句を生成する.
     * 
     * @return string
     */
    public function buildParameterClause(): string
    {
        $where = new StringObject();
        if ($this->isNot) {
            $where->append("NOT ");
        }
        $where->append($this->column);
        $where->append(" ");
        $comparisonObject = new StringObject($this->comparison);
        switch (true) {
            case $comparisonObject->equals("BETWEEN"):
                $where->append($this->comparison);
                $where->append(" ? AND ?");
                break;
            case $comparisonObject->equals("IN"):
                $where->append($this->comparison);
                $where->append(" (");
                $valuesHash = new Hash($this->values);
                for ($i = 0; $i < $valuesHash->size(); $i++) {
                    if ($i > 0) {
                        $where->append(", ");
                    }
                    $where->append("?");
                }
                $where->append(")");
                break;
            case $comparisonObject->equals("IS NULL"):
                $where->append($this->comparison);
                break;
            default:
                $where->append($this->comparison);
                $where->append(" ?");
                break;
        }
        return $where;
    }
    
}