<?php
namespace hirohiro716\Scent\Validate;

use hirohiro716\Scent\Hash;
use Iterator;
use hirohiro716\Scent\StringObject;

/**
 * プロパティの検証に失敗した場合の例外クラス。
 * 
 * @author hiro
 */
class PropertyValidationException extends ValidationException implements Iterator
{
    
    /**
     * プロパティの検証例外を作成する。
     * 
     * @param string $message スローする例外メッセージ
     * @param int $code 例外コード
     * @param Throwable 以前に使われた例外(例外の連結に使用する)
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        $newMessage = $message;
        if ($newMessage === null) {
            $newMessage = "Validation failed of record information.";
        }
        parent::__construct($newMessage, $code, $previous);
        $this->causeProperties = new Hash();
    }
    
    private $causeProperties;
    
    /**
     * 例外の原因となったプロパティを追加する。
     * 
     * @param CauseProperty $causeProperty
     */
    public function addCauseProperty(CauseProperty $causeProperty): void
    {
        $this->causeProperties->add($causeProperty);
    }
    
    /**
     * 例外の原因となったすべてのプロパティを取得する。
     * 
     * @return Hash CausePropertyの連想配列
     */
    public function getCauseProperties(): Hash
    {
        return $this->causeProperties;
    }
    
    /**
     * 例外の原因となったプロパティとメッセージの連想配列を取得する。
     * 
     * @return Hash
     */
    public function toArrayOfCauseMessages(): Hash
    {
        $hash = new Hash();
        foreach ($this->causeProperties as $causeProperty) {
            $hash->put($causeProperty->getProperty()->getPhysicalName(), $causeProperty->getMessage());
        }
        return $hash;
    }
    
    /**
     * 例外の詳細メッセージを取得する。
     * 
     * @return string メッセージ
     */
    public function getDetailMessage(): string
    {
        $message = new StringObject(parent::getMessage());
        if ($this->causeProperties->size() > 0) {
            $message->append("\r\n");
        }
        foreach ($this->causeProperties as $causeProperty) {
            $message->append("\r\n");
            $message->append($causeProperty->getMessage());
        }
        return $message;
    }
    
    /*
     * ***********************************
     * ここからIteratorインターフェースの実装。
     * ************************************
     */
    private $position = 0;
    
    /**
     * 現在の要素を返す。
     */
    public function current(): CauseProperty
    {
        $key = $this->causeProperties->getKeys()[$this->position];
        return $this->causeProperties->get($key);
    }
    
    /**
     * 現在の要素のキーを返す。
     *
     * @return string
     */
    public function key()
    {
        return $this->causeProperties->getKeys()[$this->position];
    }
    
    /**
     * 次の要素に進む。
     */
    public function next()
    {
        $this->position ++;
    }
    
    /**
     * イテレータの最初の要素に巻き戻す。
     */
    public function rewind()
    {
        $this->position = 0;
    }
    
    /**
     * 現在位置が有効な場合はtrueを返す。
     *
     * @return boolean
     */
    public function valid()
    {
        $key = $this->causeProperties->getKeys()[$this->position];
        return $this->causeProperties->existsKey($key);
    }
}

