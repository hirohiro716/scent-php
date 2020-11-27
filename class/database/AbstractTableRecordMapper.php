<?php
namespace hirohiro716\Scent\Database;

use Exception;

use hirohiro716\Scent\Hash;
use hirohiro716\Scent\StringObject;

/**
 * テーブルレコードをオブジェクトにマッピングする抽象クラス。
 * 
 * @author hiro
 */
abstract class AbstractTableRecordMapper extends AbstractTableMapper
{
    
    /**
     * コンストラクタ。
     * 
     * @param AbstractDatabase $database 接続済みAbstractDatabaseインスタンス
     */
    public function __construct($database)
    {
        parent::__construct($database);
        $this->setDefaultRecord();
    }
    
    private $record;
    
    /**
     * レコード情報を取得する。
     * 
     * @return Hash
     */
    public function getRecord(): Hash
    {
        return $this->record;
    }
    
    /**
     * レコード情報をセットする。
     * 
     * @param Hash $record
     */
    public function setRecord(Hash $record): void
    {
        $this->record = $record;
    }
    
    /**
     * 初期レコード情報をセットする。
     */
    public function setDefaultRecord(): void
    {
        $this->record = $this->createDefaultRecord();
    }
    
    /**
     * 新しくレコード情報をデータベースに追加する。
     */
    public function insert(): void
    {
        $hash = new Hash();
        foreach ($this->getColumns() as $column) {
            if ($this->record->isExistKey($column->getPhysicalName())) {
                $hash->put($column, $this->record->get($column));
            }
        }
        $this->getDatabase()->insert($hash, $this->getTableName());
    }
    
    /**
     * レコード情報を取得して編集を開始する。
     * 
     * @throws DataNotFoundException
     */
    public function edit(): void
    {
        if ($this->whereSetIsNull()) {
            throw new Exception("Search condition is not specified.");
        }
        $sql = new StringObject("SELECT * FROM ");
        $sql->append($this->getTableName());
        $sql->append(" WHERE ");
        $sql->append($this->getWhereSet()->buildParameterClause());
        $sql->append(";");
        $this->setRecord($this->getDatabase()->fetchRecord($sql, $this->getWhereSet()->buildParameters()));
        if ($this->isDeleted() == true) {
            throw new DataNotFoundException();
        }
    }
    
    /**
     * 編集のため取得したレコードが削除済みかどうかの判定メソッド。<br>
     * これはeditメソッドから自動的に呼び出され編集するかの判定に使われる。
     * 
     * @return bool 削除済みの場合はtrue
     */
    public abstract function isDeleted(): bool;
    
    /**
     * 編集開始されているレコードを保持している連想配列で上書きする。
     */
    public function update(): void
    {
        if ($this->whereSetIsNull()) {
            throw new Exception("Search condition is not specified.");
        }
        $hash = new Hash();
        foreach ($this->getColumns() as $column) {
            if ($this->record->isExistKey($column->getPhysicalName())) {
                $hash->put($column, $this->record->get($column));
            }
        }
        $this->getDatabase()->update($hash, $this->getTableName(), $this->getWhereSet());
    }
    
    /**
     * レコードを物理削除する。
     */
    protected function physicalDelete(): void
    {
        if ($this->whereSetIsNull()) {
            throw new Exception("Search condition is not specified.");
        }
        $whereSet = $this->getWhereSet();
        $sql = new StringObject("DELETE FROM ");
        $sql->append($this->getTableName());
        $sql->append(" WHERE ");
        $sql->append($whereSet->buildParameterClause());
        $sql->append(";");
        $this->getDatabase()->execute($sql, $whereSet->buildParameters());
    }
    
    /**
     * 編集のため取得したレコードを削除する。
     */
    public abstract function delete(): void;
    
    /**
     * レコードが存在する場合はtrueを返す。
     * 
     * @return bool 
     */
    public function isExist(): bool
    {
        if ($this->whereSetIsNull()) {
            throw new Exception("Search condition is not specified.");
        }
        $whereSet = $this->getWhereSet();
        $sql = new StringObject("SELECT * FROM ");
        $sql->append($this->getTableName());
        $sql->append(" WHERE ");
        $sql->append($whereSet->buildParameterClause());
        $sql->append(";");
        $backupRecord = $this->record;
        try {
            $this->setRecord($this->getDatabase()->fetchRecord($sql, $whereSet->buildParameters()));
            return true;
        } catch (Exception $exception) {
            return false;
        } finally {
            $this->setRecord($backupRecord);
        }
    }
    
}