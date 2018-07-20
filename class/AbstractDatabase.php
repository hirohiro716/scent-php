<?php
namespace hirohiro716\Scent;

use PDO;
use PDOException;

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
    protected abstract function buildConnectionString(): string;

    /**
     * データベースに接続する.
     */
    public function connect(): void
    {
        $this->pdo = new PDO($this->buildConnectionString());
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // インジェクション対策
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーはExceptionでください
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
        $statement->execute($parameters);
        $row = $statement->fetch(PDO::FETCH_NUM);
        if ($row === false) {
            throw new DataNotFoundException();
        }
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
        $statement->execute($parameters);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            throw new DataNotFoundException();
        }
        return $row;
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
        $statement->execute($parameters);
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
     * @param Hash $values
     *            INSERTする連想配列（カラム名=>値）
     * @param string $tableName
     *            テーブル名
     */
    public function insert(Hash $values, string $tableName): void
    {
        $sql = new StringObject("INSERT INTO ");
        $sql->append($tableName);
        $sql->append(" (");
        $columnsString = new StringObject();
        $valuesString = new StringObject();
        foreach ($values->getKeys() as $columnName) {
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
        $sql->append(");");
        $this->execute($sql, $values->getValues());
    }

    /**
     * 連想配列の情報でテーブルを更新する.
     *
     * @param Hash $values
     *            更新する情報の連想配列（カラム名=>値）
     * @param string $tableName
     *            テーブル名
     * @param WhereSet $whereSet
     *            更新対象レコードを特定する検索条件
     */
    public function update(Hash $values, string $tableName, WhereSet $whereSet): void
    {
        $sql = new StringObject("UPDATE ");
        $sql->append($tableName);
        $sql->append(" SET ");
        $valuesString = new StringObject();
        foreach ($values->getKeys() as $columnName) {
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
        $parameters = $values->getValues();
        foreach ($whereSet->buildParameters() as $parameter) {
            $parameters[] = $parameter;
        }
        $this->execute($sql, $parameters);
    }

    /**
     * トランザクションを開始する.
     */
    public abstract function beginTransaction(): void;

    /**
     * トランザクションをコミットする.
     */
    public abstract function commit(): void;

    /**
     * トランザクションをロールバックする.
     */
    public abstract function rollback(): void;

    /**
     * テーブルが存在するか確認する.
     *
     * @param string $tableName
     * @return array
     */
    public abstract function isExistTable(string $tableName): array;

    /**
     * テーブルのカラムをすべて取得する.
     *
     * @param string $tableName
     * @return array
     */
    public abstract function fetchColumns(string $tableName): array;

    /**
     * 連想配列を元にCASE文を作成する.
     *
     * @param Hash $hash
     *            連想配列
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

/**
 * データが存在しない場合の例外クラス.
 *
 * @author hiro
 */
class DataNotFoundException extends PDOException
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        $newMessage = $message;
        if ($newMessage === null) {
            $newMessage = "Row is not exist.";
        }
        parent::__construct($newMessage, $code, $previous);
    }

    
}
