<?php

/**
 * 連想配列オブジェクトクラス.
 * @author hiro
 */
class ArrayObject {
    
    private $array;
    
    /**
     * コンストラクタ.
     * @param array $array
     */
    public function __construct(array $array) {
        $this->array = $array;
    }
    
    /**
     * 内部に配列をセットする.
     * @param array $array
     */
    public function setArray(array $array) {
        $this->array = $array;
    }
    
    /**
     * 内部の配列を取得する.
     * @return array
     */
    public function getArray() {
        return $this->array;
    }
    
    /**
     * キーと対する値をセットする.
     * @param string $key
     * @param object $value
     */
    public function put(string $key, object $value) {
        $this->array[$key] = $value;
    }
    
    /**
     * キーに対する値を取得する.
     * @param string $key
     * @return object
     */
    public function get(string $key) {
        return $this->array[$key];
    }
    
}