<?php
namespace hirohiro716\Scent\Database;

use Exception;

use hirohiro716\Scent\Hash;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\Hashes;

/**
 * 複数のテーブル行をオブジェクト配列にマッピングする抽象クラス.
 * 
 * @author hiro
 */
abstract class AbstractBindTableRows extends AbstractBindTable
{
    
    /**
     * コンストラクタ.
     *
     * @param AbstractDatabase $database 接続済みAbstractDatabaseインスタンス.
     */
    public function __construct($database)
    {
        parent::__construct($database);
        $this->rows = new Hashes();
    }
    
    private $rows;
    
    /**
     * すべての行情報を取得する.
     * 
     * @return array
     */
    public function getRows(): Hashes
    {
        return $this->rows;
    }
    
    /**
     * 行情報を追加する.
     * 
     * @param Hash $row
     */
    public function addRow(Hash $row): void
    {
        $this->rows->add($row);
    }
    
    /**
     * 行情報を削除する.
     * 
     * @param Hash $row
     */
    public function removeRow(Hash $row): void
    {
        $this->rows->remove($row);
    }
    
    /**
     * すべての行情報をクリアする.
     */
    public function clearRows(): void
    {
        $this->rows->clear();
    }
    
    /**
     * WhereSetに従って取得したすべての行情報を取得して編集を開始する.
     * 
     * @param string ...$orderByColumns 並び替えを指定（ASC・DESCを含むカラム名）
     */
    public function edit(string ...$orderByColumns): void
    {
        $afterWherePart = new StringObject();
        $columns = new Hash($orderByColumns);
        if ($columns->size() > 0) {
            $afterWherePart->append(" ORDER BY ");
            $afterWherePart->append($columns->join(", "));
        }
        $this->clearRows();
        $sql = new StringObject("SELECT * FROM ");
        $sql->append($this->getTableName());
        if ($this->whereSetIsNull() == false) {
            // 検索条件ありの複数レコード編集
            $whereSet = $this->getWhereSet();
            $sql->append(" WHERE ");
            $sql->append($whereSet->buildParameterClause());
            $sql->append($afterWherePart);
            $this->rows = $this->getDatabase()->fetchRows($sql, $whereSet->buildParameters());
        } else {
            // 検索条件なしの全レコード編集
            if ($this->isPermittedSearchConditioEmptyUpdate() == false) {
                throw new Exception("All records edit is not permited.");
            }
            $sql->append($afterWherePart);
            $this->rows = $this->getDatabase()->fetchRows($sql);
        }
    }
    
    /**
     * 検索条件が空の状態の上書き（全レコード置き換え）を許可するかどうか.
     * @return bool 許可する場合はtrue
     */
    public abstract function isPermittedSearchConditioEmptyUpdate(): bool;
    
    /**
     * 編集している複数のレコードを保持している連想配列に置き換える.
     */
    public function update(): void
    {
        $sql = new StringObject("DELETE FROM ");
        $sql->append($this->getTableName());
        if ($this->whereSetIsNull() == false) {
            $sql->append(" WHERE ");
            $sql->append($this->getWhereSet()->buildParameterClause());
            $sql->append(";");
            $this->getDatabase()->execute($sql, $this->getWhereSet()->buildParameters());
        } else {
            if ($this->isPermittedSearchConditioEmptyUpdate() == false) {
                throw new Exception("All records update is not permited.");
            }
            $sql->append(";");
            $this->getDatabase()->execute($sql);
        }
        foreach ($this->getRows() as $row) {
            $hash = new Hash();
            foreach ($this->getColumns() as $column) {
                if ($row->isExistKey($column)) {
                    $hash->put($column, $row->get($column));
                }
            }
            $this->getDatabase()->insert($hash, $this->getTableName());
        }
    }
    
    /**
     * レコードが存在するか確認する.
     * @return bool 存在するかどうか
     */
    public function isExist(): bool {
        if ($this->whereSetIsNull() && $this->isPermittedSearchConditioEmptyUpdate() == false) {
            throw new Exception("Search condition is not specified.");
        }
        $whereSet = $this->getWhereSet();
        $sql = new StringObject("SELECT COUNT(");
        $sql->append($whereSet->getWheres()[0]->getColumn());
        $sql->append(") FROM ");
        $sql->append($this->getTableName());
        $sql->append(" WHERE ");
        $sql->append($whereSet->buildParameterClause());
        $sql->append(";");
        $result = new StringObject($this->getDatabase()->fetchOne($sql, $whereSet->buildParameters()));
        if ($result->toInteger() > 0) {
            return true;
        }
        return false;
    }
    
}