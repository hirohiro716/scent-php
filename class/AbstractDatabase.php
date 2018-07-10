<?php
namespace hirohiro716\Scent;

use PDO;

/**
 * データベースをPDOで操作する抽象クラス.
 *
 * @author hiro
 */
abstract class AbstractDatabase
{
    
    private $pdo;
    
    /**
     * PDOインスタンスを取得する.
     * 
     * @return PDO
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }
    
    /**
     * PDOインスタンスをセットする.
     * 
     * @param PDO $pdo
     */
    public function setPDO(PDO $pdo): void
    {
        $this->pdo = $pdo;
    }
    
    /**
     * PDOでデータベースに接続するための接続文字列を取得する.
     * 
     * @return string 接続文字列
     */
    abstract public function buildConnectionString(): string;
    
    /**
     * データベースに接続する.
     */
    public function connect(): void
    {
        $this->pdo = new PDO($this->buildConnectionString());
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
    
    /**
     * 更新系のSQLを実行して更新されたレコード数を取得する.
     * 
     * @param string $sql
     * @param array $parameters
     * @return int
     */
    public function execute(string $sql, array $parameters = array()): int
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);
        return $statement->rowCount();
    }
    
    /**
     * SELECT文が取得した最初のレコードの最初のカラムの値を取得する.
     * 
     * @param string $sql
     * @param array $parameters
     * @return mixed
     */
    public function fetchOne(string $sql, array $parameters = array())
    {
        $statement = $this->pdo->prepare($sql);
        $result = $statement->execute($params);
        $row = $statement->fetch(PDO::FETCH_NUM);
        return $row[0];
    }
    
    /**
     * SELECT文が取得した最初のレコードの配列を取得する.
     * 
     * @param string $sql
     * @param array $parameters
     * @return array
     */
    public function fetchRow(string $sql, array $parameters = array()): array
    {
        $statement = $this->pdo->prepare($sql);
        $result = $statement->execute($params);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * SELECT文が取得したすべてのレコードの配列を取得する.
     * 
     * @param string $sql
     * @param array $parameters
     * @return array
     */
    public function fetchRows(string $sql, array $parameters = array()): array
    {
        $statement = $this->pdo->prepare($sql);
        $result = $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * テーブルのレコード数を取得する.
     * 
     * @param string $tableName
     * @return int
     */
    public function count(string $tableName): int
    {
        return $this->fetchOne("SELECT COUNT(*) FROM " . $tableName . ";");
    }
    
    /**
     * 連想配列の情報をテーブルに追加する.
     * 
     * @param Hash $values INSERTする連想配列（カラム名=>値）
     * @param string $tableName テーブル名
     */
    public function insert(Hash $values, string $tableName): void
    {
        $sql = new StringObject("INSERT INTO ");
        $sql->append($tableName);
        $sql->append(" (");
        $columnsString = new StringObject();
        $valuesString = new StringObject();
        foreach ($values as $columnName => $value) {
            if ($columnsString->length() > 0) {
                $columnsString->append(", ");
                $valuesString->append(", ");
            }
            $columnsString->append($columnName);
            $valuesString->append("?");
        }
        $sql->append($columnsString);
        $sql->append(") VALUES (");
        $sql->append($valuesString);
        $sql->append(";");
        $this->execute($sql, $values->getValues());
    }
    
    /**
     * 連想配列の情報でテーブルを更新する.
     * 
     * @param Hash $values 更新する情報の連想配列（カラム名=>値）
     * @param string $tableName テーブル名
     * @param WhereSet $whereSet 更新対象レコードを特定する検索条件
     */
    public function update(Hash $values, string $tableName, WhereSet $whereSet): void
    {
        $sql = new StringObject("UPDATE ");
        $sql->append($tableName);
        $sql->append(" SET ");
        $valuesString = new StringObject();
        foreach ($values as $columnName => $value) {
            if ($valuesString->length() > 0) {
                $valuesString->append(", ");
            }
            $valuesString->append($columnName);
            $valuesString->append(" = ?");
        }
        $sql->append($valuesString);
        $sql->append(" WHERE ");
        $sql->append($whereSet->buildParameterClause());
        $sql->append(";");
        $parameters = $values->getArray();
        foreach ($whereSet->buildParameters() as $parameter) {
            $parameters[] = $parameter;
        }
        $this->execute($sql, $parameters);
    }
    
    /**
     * トランザクションを開始する.
     */
    abstract public function beginTransaction(): void;
    
    /**
     * トランザクションをコミットする.
     */
    abstract public function commit(): void;
    
    /**
     * トランザクションをロールバックする.
     */
    abstract public function rollback(): void;
    
    /**
     * 連想配列を元にCASE文を作成する.
     * 
     * @param Hash $hash 連想配列
     * @return string
     */
    public static function makeCaseSqlFromHash(string $columnName, Hash $hash): string
    {
        if ($hash->size() == 0) {
            return $columnName;
        }
        $case = new StringObject("CASE ");
        $case->append($columnName);
        $case->append(" ");
        foreach ($hash as $key => $value) {
            $case->append("WHEN ");
            $case->append($key);
            $case->append(" THEN '");
            $case->append($value);
            $case->append("' ");
        }
        $case->append("END");
        return $case;
    }
    
}