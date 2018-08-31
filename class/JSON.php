<?php
namespace hirohiro716\Scent;

/**
 * JSONを扱うクラス.
 * 
 * @author hiro
 */
class JSON extends AbstractObject {
    
    private $array;
    
    /**
     * コンストラクタ.
     * 
     * @param string $json
     */
    public function __construct(string $json)
    {
        $this->array = json_decode($json, true);
    }
    
    public function __toString(): string
    {
        return json_encode($this->array);
    }
    
    /**
     * 配列に変換して取得する.
     * 
     * @return array
     */
    public function toArray(): array
    {
        return $this->array;
    }
    
    /**
     * 配列からJSONを生成する.
     * 
     * @param array $array
     * @return JSON
     */
    public static function fromArray(array $array): JSON
    {
        $encoded = json_encode($array);
        return new JSON($encoded);
    }
    
}
