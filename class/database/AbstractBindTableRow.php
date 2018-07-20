<?php
namespace hirohiro716\Scent\Database;

use Exception;

use hirohiro716\Scent\Hash;
use hirohiro716\Scent\StringObject;

/**
 * テーブル行をオブジェクトにマッピングする抽象クラス.
 * 
 * @author hiro
 */
abstract class AbstractBindTableRow extends AbstractBindTable
{
    
    /**
     * コンストラクタ.
     * 
     * @param AbstractDatabase $database 接続済みAbstractDatabaseインスタンス.
     */
    public function __construct(AbstractDatabase $database)
    {
        parent::__construct($database);
        $this->setDefaultRow();
    }
    
    private $row;
    
    /**
     * 行情報を取得する.
     * 
     * @return Hash
     */
    public function getRow(): Hash
    {
        return $this->row;
    }
    
    /**
     * 行情報をセットする.
     * 
     * @param Hash $row
     */
    public function setRow(Hash $row): void
    {
        $this->row = $row;
    }
    
    /**
     * 初期値をセットする.
     */
    public function setDefaultRow(): void
    {
        $this->row = $this->createDefaultRow();
    }
    
    /**
     * 新しく行情報をデータベースに追加する.
     */
    public function insert(): void
    {
        $this->getDatabase()->insert($this->row, $this->getTableName());
    }
    
    /**
     * WhereSetに従って取得して行情報を連想配列として取得して内部にセットして排他処理する.
     * 
     * @return array 編集した行情報
     */
    protected abstract function fetchEditRowAndHold(): void;
    
    /**
     * 行情報を取得して編集を開始する.
     * 
     * @throws DataNotFoundException
     */
    public function edit(): void
    {
        $this->fetchEditRowAndHold();
        if ($this->isDeleted() == true) {
            throw new DataNotFoundException();
        }
    }
    
    /**
     * 編集のため取得したレコードが削除済みかどうかの判定メソッド. これはeditメソッドから自動的に呼び出され編集するかの判定に使われる.
     * 
     * @return bool 削除済みかどうか
     */
    public abstract function isDeleted(): bool;
    
    /**
     * 編集開始されているレコードを保持している連想配列で上書きする.
     */
    public function update(): void
    {
        $this->getDatabase()->update($this->row, $this->getTableName(), $this->getWhereSet());
    }
    
    /**
     * レコードを物理削除する.
     */
    protected function physicalDelete(): void
    {
        try {
            $whereSet = $this->getWhereSet();
        } catch (Exception $exception) {
            throw new Exception("Search condition is not specified.");
        }
        $sql = new StringObject("DELETE FROM ");
        $sql->append($this->getTableName());
        $sql->append(" WHERE ");
        $sql->append($whereSet->buildParameterClause());
        $sql->append(";");
        $this->getDatabase()->execute($sql, $whereSet->buildParameters());
    }
    
    /**
     * 編集のため取得したレコードを削除する.
     */
    public abstract function delete(): void;
    
    /**
     * レコードが存在するか確認する.
     * 
     * @return bool 
     */
    public function isExist(): bool
    {
        try {
            $whereSet = $this->getWhereSet();
        } catch (Exception $exception) {
            throw new Exception("Search condition is not specified.");
        }
        $sql = new StringObject("SELECT * FROM ");
        $sql->append($this->getTableName());
        $sql->append(" WHERE ");
        $sql->append($whereSet->buildParameterClause());
        $sql->append(";");
        $backupRow = $this->row;
        try {
            $this->setRow($this->getDatabase()->fetchRow($sql, $whereSet->buildParameters()));
            return true;
        } catch (Exception $exception) {
            return false;
        } finally {
            $this->setRow($backupRow);
        }
    }
    
}