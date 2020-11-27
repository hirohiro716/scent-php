<?php
namespace hirohiro716\Scent\Database;

use Exception;

use hirohiro716\Scent\Hash;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\Hashes;

/**
 * 複数のテーブルレコードをオブジェクト配列にマッピングする抽象クラス。
 * 
 * @author hiro
 */
abstract class AbstractTableRecordsMapper extends AbstractTableMapper
{
    
    /**
     * コンストラクタ。
     *
     * @param AbstractDatabase $database 接続済みAbstractDatabaseインスタンス。
     */
    public function __construct($database)
    {
        parent::__construct($database);
        $this->records = new Hashes();
    }
    
    private $records;
    
    /**
     * すべてのレコード情報を取得する。
     * 
     * @return array
     */
    public function getRecords(): Hashes
    {
        return $this->records;
    }
    
    /**
     * レコード情報を追加する。
     * 
     * @param Hash $record
     */
    public function addRecord(Hash $record): void
    {
        $this->records->add($record);
    }
    
    /**
     * レコード情報を削除する。
     * 
     * @param Hash $record
     */
    public function removeRecord(Hash $record): void
    {
        $this->records->remove($record);
    }
    
    /**
     * すべてのレコード情報をクリアする。
     */
    public function clearRecords(): void
    {
        $this->records->clear();
    }
    
    /**
     * WhereSetに従って取得したすべてのレコード情報を取得して編集を開始する。
     * 
     * @param string ...$orderByColumns 並び替えを指定(ASC・DESCを含むカラム名)
     */
    public function edit(string ...$orderByColumns): void
    {
        $afterWherePart = new StringObject();
        $columns = new Hash($orderByColumns);
        if ($columns->size() > 0) {
            $afterWherePart->append(" ORDER BY ");
            $afterWherePart->append($columns->join(", "));
        }
        $this->clearRecords();
        $sql = new StringObject("SELECT * FROM ");
        $sql->append($this->getTableName());
        if ($this->whereSetIsNull() == false) {
            // 検索条件ありの複数レコード編集
            $whereSet = $this->getWhereSet();
            $sql->append(" WHERE ");
            $sql->append($whereSet->buildParameterClause());
            $sql->append($afterWherePart);
            $this->records = $this->getDatabase()->fetchRows($sql, $whereSet->buildParameters());
        } else {
            // 検索条件なしの全レコード編集
            if ($this->isPermittedSearchConditioEmptyUpdate() == false) {
                throw new Exception("All records edit is not permited.");
            }
            $sql->append($afterWherePart);
            $this->records = $this->getDatabase()->fetchRows($sql);
        }
    }
    
    /**
     * 検索条件が空の状態の上書き(全レコード置き換え)を許可する場合はtrueを返す。
     * 
     * @return bool
     */
    public abstract function isPermittedSearchConditioEmptyUpdate(): bool;
    
    /**
     * 編集している複数のレコードを保持している連想配列に置き換える。
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
        foreach ($this->getRecords() as $row) {
            $hash = new Hash();
            foreach ($this->getColumns() as $column) {
                if ($row->isExistKey($column->getPhysicalName())) {
                    $hash->put($column, $row->get($column));
                }
            }
            $this->getDatabase()->insert($hash, $this->getTableName());
        }
    }
    
    /**
     * レコードが存在する場合はtrueを返す。
     * 
     * @return bool
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