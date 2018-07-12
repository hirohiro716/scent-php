<?php
namespace hirohiro716\Scent;

/**
 * SQLiteをPDOで操作するクラス.
 *
 * @author hiro
 */
class SQLite extends AbstractDatabase
{

    /**
     * トランザクション開始時にはロックを取得せずデータの読み込み/書き込みをする時点までロック取得を延期する
     */
    public const DEFERRED = "DEFERRED";

    /**
     * トランザクション開始時にRESERVEDロックを取得する
     */
    public const IMMEDIATE = "IMMEDIATE";

    /**
     * トランザクション開始時にEXCLUSIVEロックを取得する.
     */
    public const EXCLUSIVE = "EXCLUSIVE";

    /**
     * コンストラクタ.
     *
     * @param string $databaseLocation
     *            SQLiteデータベースのファイルパス
     */
    public function __construct(string $databaseLocation)
    {
        $this->databaseLocation = $databaseLocation;
    }

    private $databaseLocation;

    public function buildConnectionString(): string
    {
        return "sqlite:" . $this->databaseLocation;
    }

    private $isolationLabel = self::IMMEDIATE;

    /**
     * トランザクションの分離レベルを設定する.
     *
     * @param string $isolationLevel
     *            SQLite::DEFERRED/SQLite::IMMEDIATE/SQLite::EXCLUSIVE
     */
    public function setIsolationLabel(string $isolationLevel): void
    {
        $this->isolationLabel = $isolationLevel;
    }
    
    public function beginTransaction(): void
    {
        $this->execute("BEGIN " . $this->isolationLabel . ";");
    }
    
    public function rollback(): void
    {
        $this->execute("ROLLBACK;");
    }
    
    public function commit(): void
    {
        $this->execute("COMMIT;");
    }
    
    public function isExistTable(string $tableName): array
    {
        $parameters = array($tableName);
        $tableCount = $this->fetchOne("SELECT COUNT(*) FROM sqlite_master WHERE type='table' and name = ?;", $parameters);
        return ($tableCount == 1);
    }
    
    public function fetchColumns(string $tableName): array
    {
        $parameters = array($tableName);
        return $this->fetchRow("PRAGMA table_info(?);", $parameters);
    }
}