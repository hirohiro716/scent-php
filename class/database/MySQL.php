<?php
namespace hirohiro716\Scent\Database;

use PDO;

use hirohiro716\Scent\StringObject;

/**
 * MySQLをPDOで操作するクラス。
 *
 * @author hiro
 */
class MySQL extends AbstractDatabase
{
    
    /**
     * MySQLにはBoolean型が無いのでTINYINT(1)で代用する際の有効を表す数値。
     */
    public const BOOLEAN_VALUE_ENABLED = 1;
    
    /**
     * MySQLにはBoolean型が無いのでTINYINT(1)で代用する際の無効を表す数値。
     */
    public const BOOLEAN_VALUE_DISABLED = 0;
    
    /**
     * コンストラクタ。
     *
     * @param string $host データベースホスト
     * @param string $databaseName データベース名
     * @param string $username ユーザー名
     * @param string $password パスワード
     * @param string $charset 文字セット(デフォルトはutf8mb4)
     */
    public function __construct(string $host, string $databaseName, string $username, string $password, string $charset = "utf8mb4")
    {
        $this->host = $host;
        $this->databaseName = $databaseName;
        $this->username = $username;
        $this->password = $password;
        $this->charset = $charset;
    }
    
    private $host;
    
    private $databaseName;
    
    private $username;
    
    private $password;
    
    private $charset;
    
    public function buildConnectionString(): string
    {
        $dsn = new StringObject("mysql:host=");
        $dsn->append($this->host);
        $dsn->append(";dbname=");
        $dsn->append($this->databaseName);
        $dsn->append(";charset=");
        $dsn->append($this->charset);
        return $dsn;
    }
    
    public function connect(): void
    {
        $pdo = new PDO($this->buildConnectionString(), $this->username, $this->password);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // インジェクション対策
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーはExceptionでください
        $this->setPDO($pdo);
    }
    
    public function fetchLastAutoIncrementID()
    {
        return $this->fetchOne("SELECT LAST_INSERT_ID();");
    }
    
    public function beginTransaction(): void
    {
        $this->getPDO()->beginTransaction();
    }
    
    public function rollback(): void
    {
        $this->getPDO()->rollBack();
    }
    
    public function commit(): void
    {
        $this->getPDO()->commit();
    }
    
    public function existsTable(string $tableName): bool
    {
        $parameters = array($tableName);
        $tableCount = $this->fetchOne("SELECT count(*) FROM information_schema.TABLES WHERE TABLE_NAME = ?;", $parameters);
        return ($tableCount == 1);
    }
    
    public function fetchColumns(string $tableName): array
    {
        $parameters = array($tableName);
        $sql = new StringObject("SHOW COLUMNS FROM ");
        $sql->append($this->databaseName);
        $sql->append(".");
        $sql->append($tableName);
        $sql->append(";");
        $records = $this->fetchRecords($sql, $parameters);
        $columns = array();
        foreach ($records as $record) {
            $columns[] = $record->get("Field");
        }
        return $columns;
    }
}