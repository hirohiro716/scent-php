<?php
namespace hirohiro716\Scent\Database;

use hirohiro716\Scent\Hash;
use hirohiro716\Scent\Hashes;
use hirohiro716\Scent\StringObject;
use hirohiro716\Scent\AbstractObject;
use hirohiro716\Scent\Helper;

/**
 * テーブルとオブジェクトをマッピングする抽象クラス.
 * 
 * @author hiro
 */
abstract class AbstractBindTable extends AbstractObject
{
    
    /**
     * コンストラクタ.
     * 
     * @param AbstractDatabase $database 接続済みAbstractDatabaseインスタンス.
     */
    public function __construct($database)
    {
        parent::__construct();
        $this->database = $database;
    }
    
    private $database;
    
    /**
     * コンストラクタで指定したAbstractDatabaseインスタンスを取得する.
     * 
     * @return AbstractDatabase
     */
    public function getDatabase(): AbstractDatabase
    {
        return $this->database;
    }
    
    /**
     * テーブル名を取得する.
     * 
     * @return string
     */
    public abstract function getTableName(): string;
    
    /**
     * テーブル名を取得する.
     * 
     * @return string
     */
    public static function getTableNameStatic(): string
    {
        $table = new static(null);
        return $table->getTableName();
    }
    
    /**
     * すべてのカラム定数を取得する.
     * 
     * @return Columns
     */
    public abstract function getColumns(): Columns;
    
    /**
     * カラムの物理名をキー・各カラムの初期値を値とする配列を取得する.
     * 
     * @return Hash
     */
    public abstract function createDefaultRow(): Hash;
    
    private $whereSet = null;
    
    /**
     * 編集・更新・削除に使用するレコード特定用のWhereSetを取得する.
     * 
     * @return whereSet
     */
    protected function getWhereSet(): WhereSet
    {
        return $this->whereSet;
    }
    
    /**
     * 編集・更新・削除に使用するレコード特定用のWhereSetを指定する.<br>
     * 編集する前は必ずこのメソッドを使用して抽出条件を指定する.
     * 
     * @param whereSet
     */
    public function setWhereSet(WhereSet $whereSet): void
    {
        $this->whereSet = $whereSet;
    }
    
    /**
     * レコード特定用のWhereSetがセットされているか確認する.
     * 
     * @return bool
     */
    public function whereSetIsNull(): bool
    {
        return Helper::isNull($this->whereSet);
    }
    
    /**
     * 保持している連想配列がレコードとして有効か検証する.
     */
    public abstract function validate(): void;
    
    /**
     * 保持している連想配列を標準化（全角を半角に変換したり）する.
     */
    public abstract function normalize(): void;
    
    /**
     * レコードを検索して結果を２次元連想配列で取得する.
     * 
     * @param array $whereSetArray WhereSetオブジェクトの配列
     * @param string $select WHERE句より前のSELECT文
     * @param string $afterWherePart WHERE句より後のSQL
     * @return Hashes 検索結果の２次元連想配列
     */
    public function search(array $whereSetArray, string $select = "", string $afterWherePart = ""): Hashes
    {
        // SELECT句の作成
        $selectStringObject = new StringObject($select);
        $sql = new StringObject();
        if ($selectStringObject->length() == 0) {
            $sql->append("SELECT * FROM ");
            $sql->append($this->getTableName());
        } else {
            $sql->append($selectStringObject);
        }
        $sql->append(" ");
        // WHERE句の作成
        $whereSetHash = new Hash($whereSetArray);
        $wheresParameters = new Hash();
        if ($whereSetHash->size() > 0) {
            $sql->append("WHERE ");
            $wheresStringObject = new StringObject();
            foreach ($whereSetArray as $whereSet) {
                if ($wheresStringObject->length() > 0) {
                    $wheresStringObject->append(" OR ");
                }
                $wheresStringObject->append($whereSet->buildParameterClause());
                foreach ($whereSet->buildParameters() as $parameter) {
                    $wheresParameters->add($parameter);
                }
            }
            $sql->append($wheresStringObject);
        }
        // ORDER BYなどの追加
        $afterWherePartStringObject = new StringObject($afterWherePart);
        if ($afterWherePartStringObject->length() > 0) {
            $sql->append(" ");
            $sql->append($afterWherePart);
        }
        return $this->getDatabase()->fetchRows($sql, $wheresParameters->getValues());
    }
    
}
