<?php
namespace hirohiro716\Scent;

use Exception;

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
    public function __construct(AbstractDatabase $database)
    {
        parent::__construct($database);
        $this->rows = new Hash();
    }
    
    private $rows;
    
    /**
     * すべての行情報を取得する.
     * 
     * @return array
     */
    public function getRows(): array
    {
        return $this->rows->getValues();
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
        $this->rows->removeValue($row);
    }
    
    /**
     * すべての行情報をクリアする.
     */
    public function clearRows(): void
    {
        $this->rows->clear();
    }
    
    /**
     * WhereSetに従って取得したすべての行情報を連想配列として取得して内部にセットして排他処理する.
     * 
     * @param string $afterWherePart ORDER句などでFROM句の後に入力するオプション
     */
    protected abstract function fetchEditRowsAndHold(string $afterWherePart = ""): void;
    
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
            $afterWherePart->append("ORDER BY ");
            $afterWherePart->append($columns->join(", "));
        }
        $this->clearRows();
        $this->fetchEditRowsAndHold($afterWherePart);
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
        try {
            $sql->append(" WHERE ");
            $sql->append($this->getWhereSet()->buildParameterClause());
            $sql->append(";");
            $this->getDatabase()->execute($sql, $this->getWhereSet()->buildParameters());
        } catch (Exception $exception) {
            if ($this->isPermittedSearchConditioEmptyUpdate() == false) {
                throw new Exception("All records update is not permited.");
            }
            $sql->append(";");
            $this->getDatabase()->execute($sql);
        }
        foreach ($this->getRows() as $row) {
            $this->getDatabase()->insert($row, $this->getTableName());
        }
    }
    
    /**
     * レコードが存在するか確認する.
     * @return bool 存在するかどうか
     */
    public function isExist(): bool {
        try {
            $whereSet = $this->getWhereSet();
        } catch (Exception $exception) {
            if ($this->isPermittedSearchConditioEmptyUpdate() == false) {
                throw new Exception("Search condition is not specified.");
            }
        }
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